<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasPermissions, HasApiTokens;

    protected $fillable = [
        'company_id',
        'role_id',
        'department_id',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'employee_code',
        'designation',
        'department',
        'reports_to',
        'password',
        'phone',
        'avatar',
        'profile_photo',
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
     * Get the department this user belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all projects this user has access to.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'user_projects')
            ->using(UserProject::class)
            ->withPivot([
                'access_level',
                'is_available_for_leads',
                'max_active_leads',
                'current_active_leads',
                'assignment_weight',
                'last_lead_assigned_at',
                'assigned_at',
                'assigned_by'
            ])
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

    /**
     * Get the manager this user reports to.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reports_to');
    }

    /**
     * Get all direct reports (users who report to this user).
     */
    public function directReports()
    {
        return $this->hasMany(User::class, 'reports_to');
    }

    /**
     * Get all subordinates recursively (direct reports and their reports).
     */
    public function getAllSubordinates(): \Illuminate\Support\Collection
    {
        $subordinates = collect();
        
        foreach ($this->directReports as $report) {
            $subordinates->push($report);
            $subordinates = $subordinates->merge($report->getAllSubordinates());
        }
        
        return $subordinates;
    }

    /**
     * Get all managers up the chain.
     */
    public function getReportingChain(): \Illuminate\Support\Collection
    {
        $chain = collect();
        $current = $this->manager;
        
        while ($current) {
            $chain->push($current);
            $current = $current->manager;
        }
        
        return $chain;
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

    /**
     * Scope to filter by department.
     */
    public function scopeInDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to filter users in a specific project's departments.
     */
    public function scopeInProjectDepartments($query, int $projectId)
    {
        return $query->whereHas('department', function ($q) use ($projectId) {
            $q->where('project_id', $projectId);
        });
    }

    /**
     * Scope to filter by department type (management, sales, pre_sales).
     */
    public function scopeInDepartmentType($query, string $departmentType)
    {
        return $query->whereHas('department', function ($q) use ($departmentType) {
            $q->where('slug', $departmentType);
        });
    }

    // ==================== METHODS ====================

    /**
     * Check if user has access to a specific project.
     * Company-wide users (Super Admin, Admin, Sales Director) can access all company projects.
     */
    public function hasProjectAccess(int $projectId): bool
    {
        // Company-wide access users can access any project in their company
        if ($this->hasCompanyWideAccess()) {
            return \App\Models\Project::where('id', $projectId)
                ->where('company_id', $this->company_id)
                ->exists();
        }

        return $this->projects()->where('projects.id', $projectId)->exists();
    }

    /**
     * Check if user has any project assignment.
     * Company-wide users don't require explicit project assignment.
     */
    public function hasAnyProjectAssignment(): bool
    {
        // Company-wide users don't need explicit project assignment
        if ($this->hasCompanyWideAccess()) {
            return true;
        }

        return $this->projects()->exists();
    }

    /**
     * Assign user to a project.
     */
    public function assignToProject(int $projectId, ?int $assignedBy = null, string $accessLevel = 'member'): void
    {
        $this->projects()->syncWithoutDetaching([
            $projectId => [
                'access_level' => $accessLevel,
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
     * For company-wide users, returns all company projects.
     */
    public function getAccessibleProjectIds(): array
    {
        // Company-wide access users can access all company projects
        if ($this->hasCompanyWideAccess()) {
            return \App\Models\Project::where('company_id', $this->company_id)
                ->pluck('id')
                ->toArray();
        }

        return $this->projects()->pluck('projects.id')->toArray();
    }

    // ==================== LEAD RELATIONSHIPS ====================

    /**
     * Get leads assigned to this user.
     */
    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'current_assignee_id');
    }

    /**
     * Get leads owned by this user (original owner for incentives).
     */
    public function ownedLeads()
    {
        return $this->hasMany(Lead::class, 'original_owner_id');
    }

    /**
     * Get teams this user belongs to.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot(['team_role', 'is_available', 'current_active_leads'])
            ->withTimestamps();
    }

    /**
     * Get team memberships with full details.
     */
    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get teams where user is team lead.
     */
    public function ledTeams()
    {
        return $this->hasMany(Team::class, 'team_lead_id');
    }

    // ==================== ROLE-BASED ACCESS METHODS ====================

    /**
     * Check if user has admin-level access (Super Admin, Admin, Sales Director).
     */
    public function hasCompanyWideAccess(): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hierarchy_level <= 3; // Super Admin, Admin, Sales Director
    }

    /**
     * Check if user is a manager (can see team/project data).
     */
    public function isManager(): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hierarchy_level <= 6; // Up to Team Lead
    }

    /**
     * Check if user is a Team Lead.
     */
    public function isTeamLead(): bool
    {
        return $this->role?->slug === 'team_lead';
    }

    /**
     * Check if user is a Project Manager.
     */
    public function isProjectManager(): bool
    {
        return $this->role?->slug === 'project_manager';
    }

    /**
     * Check if user is a Sales Manager.
     */
    public function isSalesManager(): bool
    {
        return $this->role?->slug === 'sales_manager';
    }

    /**
     * Check if user is a Telecaller.
     */
    public function isTelecaller(): bool
    {
        return $this->role?->slug === 'telecaller';
    }

    /**
     * Check if user is a Channel Partner.
     */
    public function isChannelPartner(): bool
    {
        return $this->role?->slug === 'channel_partner';
    }

    /**
     * Get team IDs where user is a member or lead.
     */
    public function getAccessibleTeamIds(): array
    {
        $teamIds = $this->teams()->pluck('teams.id')->toArray();
        $ledTeamIds = $this->ledTeams()->pluck('id')->toArray();
        return array_unique(array_merge($teamIds, $ledTeamIds));
    }

    /**
     * Get all subordinate user IDs (for managers).
     */
    public function getSubordinateIds(): array
    {
        return $this->getAllSubordinates()->pluck('id')->toArray();
    }

    // ==================== DEPARTMENT METHODS ====================

    /**
     * Check if user belongs to a specific department type.
     */
    public function isInDepartmentType(string $departmentType): bool
    {
        return $this->department?->slug === $departmentType;
    }

    /**
     * Check if user is in management department.
     */
    public function isInManagement(): bool
    {
        return $this->isInDepartmentType('management');
    }

    /**
     * Check if user is in sales department.
     */
    public function isInSales(): bool
    {
        return $this->isInDepartmentType('sales');
    }

    /**
     * Check if user is in pre-sales department.
     */
    public function isInPreSales(): bool
    {
        return $this->isInDepartmentType('pre_sales');
    }

    /**
     * Get the project the user's department belongs to.
     */
    public function getDepartmentProject(): ?Project
    {
        return $this->department?->project;
    }

    /**
     * Get all projects accessible through user's department.
     * For company-wide users (management), returns all company projects.
     * For others, returns only their department's project.
     */
    public function getDepartmentProjects(): array
    {
        if ($this->hasCompanyWideAccess()) {
            return Project::where('company_id', $this->company_id)->pluck('id')->toArray();
        }

        $departmentProject = $this->getDepartmentProject();
        return $departmentProject ? [$departmentProject->id] : [];
    }

    /**
     * Check if user has access to a specific department.
     */
    public function hasDepartmentAccess(int $departmentId): bool
    {
        // Own department
        if ($this->department_id === $departmentId) {
            return true;
        }

        // Company-wide users can access all departments in their company
        if ($this->hasCompanyWideAccess()) {
            return Department::where('id', $departmentId)
                ->whereHas('project', function ($q) {
                    $q->where('company_id', $this->company_id);
                })
                ->exists();
        }

        return false;
    }
}
