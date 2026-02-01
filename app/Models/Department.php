<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',  // Company-level departments (e.g., Management)
        'project_id',  // Project-level departments (e.g., Sales, Pre-sales) - nullable
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Standard department types
    public const TYPE_MANAGEMENT = 'management';
    public const TYPE_SALES = 'sales';
    public const TYPE_PRE_SALES = 'pre_sales';
    public const TYPE_EXTERNAL = 'external';

    public const TYPES = [
        self::TYPE_MANAGEMENT => 'Management',
        self::TYPE_SALES => 'Sales',
        self::TYPE_PRE_SALES => 'Pre-Sales',
        self::TYPE_EXTERNAL => 'External',
    ];

    // Departments that are company-wide (above project level)
    public const COMPANY_LEVEL_DEPARTMENTS = ['management'];
    
    // Departments that are project-specific
    public const PROJECT_LEVEL_DEPARTMENTS = ['sales', 'pre_sales', 'external'];

    // ==================== RELATIONSHIPS ====================

    /**
     * Department belongs to a company (for company-wide departments like Management).
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Department belongs to a project (for project-specific departments like Sales, Pre-sales).
     * Note: This is nullable for company-level departments like Management.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Department has many roles.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Department has many users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter active departments.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by project.
     */
    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope to filter by department type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('slug', $type);
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the department type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->slug] ?? $this->name;
    }

    // ==================== METHODS ====================

    /**
     * Check if this is a company-level department (above project level).
     * Company-level departments don't require project assignment.
     */
    public function isCompanyLevel(): bool
    {
        return in_array($this->slug, self::COMPANY_LEVEL_DEPARTMENTS);
    }

    /**
     * Check if this is a project-level department.
     * Project-level departments require project assignment.
     */
    public function isProjectLevel(): bool
    {
        return in_array($this->slug, self::PROJECT_LEVEL_DEPARTMENTS);
    }

    /**
     * Check if this is a management department.
     */
    public function isManagement(): bool
    {
        return $this->slug === self::TYPE_MANAGEMENT;
    }

    /**
     * Check if this is a sales department.
     */
    public function isSales(): bool
    {
        return $this->slug === self::TYPE_SALES;
    }

    /**
     * Check if this is a pre-sales department.
     */
    public function isPreSales(): bool
    {
        return $this->slug === self::TYPE_PRE_SALES;
    }
}
