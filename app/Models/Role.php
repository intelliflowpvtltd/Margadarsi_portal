<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'hierarchy_level',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'hierarchy_level' => 'integer',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Default hierarchy levels for system roles.
     */
    public const HIERARCHY_LEVELS = [
        'super-admin' => 1,
        'admin' => 2,
        'sales-manager' => 3,
        'senior-sales-executive' => 4,
        'sales-executive' => 5,
        'team-leader' => 6,
        'tele-caller' => 7,
    ];

    /**
     * Default system roles configuration.
     */
    public const SYSTEM_ROLES = [
        [
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'System Administrator with full access to all features',
            'hierarchy_level' => 1,
        ],
        [
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Company administrator with broad access',
            'hierarchy_level' => 2,
        ],
        [
            'name' => 'Sales Manager',
            'slug' => 'sales-manager',
            'description' => 'Manages sales team and oversees deals',
            'hierarchy_level' => 3,
        ],
        [
            'name' => 'Senior Sales Executive',
            'slug' => 'senior-sales-executive',
            'description' => 'Experienced sales executive with additional privileges',
            'hierarchy_level' => 4,
        ],
        [
            'name' => 'Sales Executive',
            'slug' => 'sales-executive',
            'description' => 'Handles leads and customer interactions',
            'hierarchy_level' => 5,
        ],
        [
            'name' => 'Team Leader',
            'slug' => 'team-leader',
            'description' => 'Leads a sub-team of sales executives',
            'hierarchy_level' => 6,
        ],
        [
            'name' => 'Tele Caller',
            'slug' => 'tele-caller',
            'description' => 'Handles phone inquiries and initial lead qualification',
            'hierarchy_level' => 7,
        ],
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = Str::slug($role->name);
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the company that owns this role.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the hierarchy label.
     */
    public function getHierarchyLabelAttribute(): string
    {
        return "Level {$this->hierarchy_level}";
    }

    /**
     * Check if this role has higher authority than another.
     */
    public function hasHigherAuthorityThan(Role $otherRole): bool
    {
        return $this->hierarchy_level < $otherRole->hierarchy_level;
    }

    /**
     * Check if this role has equal or higher authority than another.
     */
    public function hasAuthorityOver(Role $otherRole): bool
    {
        return $this->hierarchy_level <= $otherRole->hierarchy_level;
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
     * Scope for active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope for custom (non-system) roles.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope to order by hierarchy (highest authority first).
     */
    public function scopeOrderByHierarchy($query, string $direction = 'asc')
    {
        return $query->orderBy('hierarchy_level', $direction);
    }

    /**
     * Scope for roles at or below a certain hierarchy level.
     */
    public function scopeAtOrBelowLevel($query, int $level)
    {
        return $query->where('hierarchy_level', '>=', $level);
    }

    // ==================== STATIC METHODS ====================

    /**
     * Create default system roles for a company.
     */
    public static function createSystemRolesForCompany(int $companyId): void
    {
        foreach (self::SYSTEM_ROLES as $roleData) {
            self::create(array_merge($roleData, [
                'company_id' => $companyId,
                'is_system' => true,
                'is_active' => true,
            ]));
        }
    }
}
