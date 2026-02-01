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
        'scope',
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
     * Scope constants.
     */
    public const SCOPE_COMPANY = 'company';
    public const SCOPE_PROJECT = 'project';
    public const SCOPE_DEPARTMENT = 'department';

    /**
     * Default hierarchy levels for system roles.
     * 
     * Lower number = higher authority
     * Company-level roles: 1-2
     * Project-level roles: 3-6
     */
    public const HIERARCHY_LEVELS = [
        'super_admin' => 1,           // Father of System (Company Level)
        'company_admin' => 2,         // Father of Company (Company Level)
        'project_manager' => 3,       // Father of Project (Project Level)
        'senior_sales_executive' => 4, // Senior Sales (Project Level)
        'team_leader' => 4,           // Pre-sales Team Lead (Project Level)
        'sales_executive' => 5,       // Sales (Project Level)
        'telecaller' => 5,            // Pre-sales Telecaller (Project Level)
        'channel_partner' => 6,       // External Lead Source (Project Level)
    ];

    /**
     * System roles configuration for seeding.
     * Scope defines whether the role is company-wide or project-specific.
     * - company: Can access all projects (Management department roles)
     * - project: Assigned to specific projects (Sales, Pre-Sales, External department roles)
     */
    public const SYSTEM_ROLES = [
        // ===== COMPANY LEVEL (Management Department) =====
        [
            'name' => 'Super Admin',
            'slug' => 'super_admin',
            'description' => 'Father of System - Full access across all companies and projects',
            'hierarchy_level' => 1,
            'scope' => self::SCOPE_COMPANY,
        ],
        [
            'name' => 'Company Admin',
            'slug' => 'company_admin',
            'description' => 'Father of Company - Full access to all projects within the company',
            'hierarchy_level' => 2,
            'scope' => self::SCOPE_COMPANY,
        ],
        // ===== PROJECT LEVEL (Sales Department) =====
        [
            'name' => 'Project Manager',
            'slug' => 'project_manager',
            'description' => 'Father of Project - Manages project operations and team',
            'hierarchy_level' => 3,
            'scope' => self::SCOPE_PROJECT,
        ],
        [
            'name' => 'Senior Sales Executive',
            'slug' => 'senior_sales_executive',
            'description' => 'Senior sales role within assigned projects',
            'hierarchy_level' => 4,
            'scope' => self::SCOPE_PROJECT,
        ],
        [
            'name' => 'Sales Executive',
            'slug' => 'sales_executive',
            'description' => 'Handles sales leads and conversions for assigned projects',
            'hierarchy_level' => 5,
            'scope' => self::SCOPE_PROJECT,
        ],
        // ===== PROJECT LEVEL (Pre-Sales Department) =====
        [
            'name' => 'Team Leader',
            'slug' => 'team_leader',
            'description' => 'Leads pre-sales team within assigned projects',
            'hierarchy_level' => 4,
            'scope' => self::SCOPE_PROJECT,
        ],
        [
            'name' => 'Telecaller',
            'slug' => 'telecaller',
            'description' => 'Handles initial lead calls and qualification for assigned projects',
            'hierarchy_level' => 5,
            'scope' => self::SCOPE_PROJECT,
        ],
        // ===== PROJECT LEVEL (External Department) =====
        [
            'name' => 'Channel Partner',
            'slug' => 'channel_partner',
            'description' => 'External lead source with limited access to assigned projects',
            'hierarchy_level' => 6,
            'scope' => self::SCOPE_PROJECT,
        ],
    ];


    /**
     * Department-based role configuration.
     * Management department roles are COMPANY scope (above project level).
     * Sales, Pre-Sales, and External department roles are PROJECT scope.
     */
    public const DEPARTMENT_ROLES = [
        // ===== COMPANY LEVEL DEPARTMENT =====
        'management' => [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Father of System - Full access across all companies and projects',
                'hierarchy_level' => 1,
                'scope' => self::SCOPE_COMPANY,
            ],
            [
                'name' => 'Company Admin',
                'slug' => 'company_admin',
                'description' => 'Father of Company - Full access to all projects within the company',
                'hierarchy_level' => 2,
                'scope' => self::SCOPE_COMPANY,
            ],
        ],
        // ===== PROJECT LEVEL DEPARTMENTS =====
        'sales' => [
            [
                'name' => 'Project Manager',
                'slug' => 'project_manager',
                'description' => 'Father of Project - Manages project operations and team',
                'hierarchy_level' => 3,
                'scope' => self::SCOPE_PROJECT,
            ],
            [
                'name' => 'Senior Sales Executive',
                'slug' => 'senior_sales_executive',
                'description' => 'Senior sales role within assigned projects',
                'hierarchy_level' => 4,
                'scope' => self::SCOPE_PROJECT,
            ],
            [
                'name' => 'Sales Executive',
                'slug' => 'sales_executive',
                'description' => 'Handles sales leads and conversions for assigned projects',
                'hierarchy_level' => 5,
                'scope' => self::SCOPE_PROJECT,
            ],
        ],
        'pre_sales' => [
            [
                'name' => 'Team Leader',
                'slug' => 'team_leader',
                'description' => 'Leads pre-sales team within assigned projects',
                'hierarchy_level' => 4,
                'scope' => self::SCOPE_PROJECT,
            ],
            [
                'name' => 'Telecaller',
                'slug' => 'telecaller',
                'description' => 'Handles initial lead calls and qualification for assigned projects',
                'hierarchy_level' => 5,
                'scope' => self::SCOPE_PROJECT,
            ],
        ],
        'external' => [
            [
                'name' => 'Channel Partner',
                'slug' => 'channel_partner',
                'description' => 'External lead source with limited access to assigned projects',
                'hierarchy_level' => 6,
                'scope' => self::SCOPE_PROJECT,
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
     * Get the computed scope based on relationships.
     * If department_id is set, scope is 'department'.
     * If project_id is set (but not department), scope is 'project'.
     * Otherwise, scope is 'company'.
     */
    public function getScopeAttribute(): string
    {
        // First check if there's an actual scope value stored
        if (!empty($this->attributes['scope'] ?? null)) {
            return $this->attributes['scope'];
        }

        // Compute scope from relationships
        if (!empty($this->department_id)) {
            return self::SCOPE_DEPARTMENT;
        }

        if (!empty($this->project_id)) {
            return self::SCOPE_PROJECT;
        }

        return self::SCOPE_COMPANY;
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
     * Check if this is a company-wide role.
     */
    public function isCompanyWide(): bool
    {
        return $this->scope === self::SCOPE_COMPANY;
    }

    /**
     * Check if this is a project-specific role.
     */
    public function isProjectSpecific(): bool
    {
        return $this->scope === self::SCOPE_PROJECT;
    }

    /**
     * Check if this is a department-specific role.
     */
    public function isDepartmentSpecific(): bool
    {
        return $this->scope === self::SCOPE_DEPARTMENT;
    }

    /**
     * Check if this is a global (company-wide) role.
     * Alias for isCompanyWide() for backward compatibility.
     */
    public function isGlobal(): bool
    {
        return $this->isCompanyWide();
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
     * Scope for company-wide roles.
     */
    public function scopeCompanyWide($query)
    {
        return $query->where('scope', self::SCOPE_COMPANY);
    }

    /**
     * Scope for global roles (company-wide, no department/project).
     * Alias for scopeCompanyWide() for backward compatibility.
     */
    public function scopeGlobal($query)
    {
        return $query->where('scope', self::SCOPE_COMPANY);
    }

    /**
     * Scope for project-specific roles.
     */
    public function scopeProjectSpecific($query)
    {
        return $query->where('scope', self::SCOPE_PROJECT);
    }

    /**
     * Scope for department-specific roles.
     */
    public function scopeDepartmentSpecific($query)
    {
        return $query->where('scope', self::SCOPE_DEPARTMENT);
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
