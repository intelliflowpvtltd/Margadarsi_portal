<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasPermissions;

    protected $fillable = [
        'company_id',
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the company that owns this user.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the role assigned to this user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all projects this user has access to.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'user_projects')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Users assigned by this user.
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'user_projects', 'assigned_by', 'project_id')
            ->withPivot('user_id', 'assigned_at');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get initials.
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to filter by role.
     */
    public function scopeWithRole($query, int $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope to filter users assigned to a specific project.
     */
    public function scopeAssignedToProject($query, int $projectId)
    {
        return $query->whereHas('projects', function ($q) use ($projectId) {
            $q->where('projects.id', $projectId);
        });
    }

    /**
     * Scope to search users by name or email.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'ilike', "%{$search}%")
                ->orWhere('last_name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('phone', 'ilike', "%{$search}%");
        });
    }

    // ==================== METHODS ====================

    /**
     * Check if user has access to a specific project.
     */
    public function hasProjectAccess(int $projectId): bool
    {
        return $this->projects()->where('projects.id', $projectId)->exists();
    }

    /**
     * Assign user to a project.
     */
    public function assignToProject(int $projectId, ?int $assignedBy = null): void
    {
        $this->projects()->syncWithoutDetaching([
            $projectId => [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy,
            ],
        ]);
    }

    /**
     * Remove user from a project.
     */
    public function removeFromProject(int $projectId): void
    {
        $this->projects()->detach($projectId);
    }

    /**
     * Get accessible project IDs.
     */
    public function getAccessibleProjectIds(): array
    {
        return $this->projects()->pluck('projects.id')->toArray();
    }
}
