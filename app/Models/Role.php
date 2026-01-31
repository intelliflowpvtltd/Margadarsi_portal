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
        'project_id',
        'department_id',
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
        'super_admin' => 1,
        'admin' => 2,
        'sales_director' => 3,
        'sales_manager' => 4,
        'project_manager' => 5,
        'team_lead' => 6,
        'telecaller' => 7,
        'channel_partner' => 8,
    ];

    /**
     * System roles configuration for seeding.
     */
    public const SYSTEM_ROLES = [
        [
            'name' => 'Super Admin',
            'slug' => 'super_admin',
            'description' => 'Full system access with all permissions',
            'hierarchy_level' => 1,
        ],
        [
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Company administrator with most permissions',
            'hierarchy_level' => 2,
        ],
        [
            'name' => 'Sales Director',
            'slug' => 'sales_director',
            'description' => 'Director of sales operations',
            'hierarchy_level' => 3,
        ],
        [
            'name' => 'Sales Manager',
            'slug' => 'sales_manager',
            'description' => 'Manages sales team and operations',
            'hierarchy_level' => 4,
        ],
        [
            'name' => 'Project Manager',
            'slug' => 'project_manager',
            'description' => 'Manages project execution and reporting',
            'hierarchy_level' => 5,
        ],
        [
            'name' => 'Team Lead',
            'slug' => 'team_lead',
            'description' => 'Leads a team of telecallers',
            'hierarchy_level' => 6,
        ],
        [
            'name' => 'Telecaller',
            'slug' => 'telecaller',
            'description' => 'Handles leads and customer calls',
            'hierarchy_level' => 7,
        ],
        [
            'name' => 'Channel Partner',
            'slug' => 'channel_partner',
            'description' => 'External partner with limited access',
            'hierarchy_level' => 8,
        ],
    ];


    /**
     * Department-based role configuration.
     */
    public const DEPARTMENT_ROLES = [
        'management' => [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Full system access across all companies',
                'hierarchy_level' => 1,
            ],
            [
                'name' => 'Company Admin',
                'slug' => 'company_admin',
                'description' => 'Company-level admin, manages all projects',
                'hierarchy_level' => 2,
            ],
        ],
        'sales' => [
            [
                'name' => 'Project Manager',
                'slug' => 'project_manager',
                'description' => 'Manages project-level operations',
                'hierarchy_level' => 3,
            ],
            [
                'name' => 'Senior Sales Executive',
                'slug' => 'senior_sales_executive',
                'description' => 'Senior sales role, manages sales team',
                'hierarchy_level' => 4,
            ],
            [
                'name' => 'Sales Executive',
                'slug' => 'sales_executive',
                'description' => 'Handles sales leads and conversions',
                'hierarchy_level' => 5,
            ],
        ],
        'pre_sales' => [
            [
                'name' => 'Team Leader',
                'slug' => 'team_leader',
                'description' => 'Leads pre-sales team activities',
                'hierarchy_level' => 4,
            ],
            [
                'name' => 'Telecaller',
                'slug' => 'telecaller',
                'description' => 'Handles initial lead calls and qualification',
                'hierarchy_level' => 5,
            ],
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
     * Role belongs to a company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Role belongs to a project (for project-specific roles).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Role belongs to a department (for department-specific roles).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Role has many permissions through role_permissions pivot.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Get all users with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
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

    /**
     * Check if this is a global (company-wide) role.
     */
    public function isGlobal(): bool
    {
        return is_null($this->department_id) && is_null($this->project_id);
    }

    /**
     * Check if this is a project-specific role.
     */
    public function isProjectSpecific(): bool
    {
        return !is_null($this->project_id) && !is_null($this->department_id);
    }

    /**
     * Check if this is a management role (hierarchy 1-2).
     */
    public function isManagementRole(): bool
    {
        return $this->hierarchy_level <= 2;
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

    /**
     * Scope to filter by department.
     */
    public function scopeForDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to filter by project.
     */
    public function scopeForProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope for global roles (company-wide, no department/project).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('department_id')->whereNull('project_id');
    }

    /**
     * Scope for project-specific roles.
     */
    public function scopeProjectSpecific($query)
    {
        return $query->whereNotNull('project_id')->whereNotNull('department_id');
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
