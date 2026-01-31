<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasProjectAccess
 * 
 * Provides project-based data visibility scoping for models.
 * Models using this trait will automatically filter data based on user's project access.
 */
trait HasProjectAccess
{
    /**
     * Scope query to only include records from projects the user has access to.
     * 
     * @param Builder $query
     * @param \App\Models\User|null $user
     * @return Builder
     */
    public function scopeForUserProjects(Builder $query, $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return no results for unauthenticated
        }

        // Super Admin, Admin, Sales Director have company-wide access
        if ($user->hasCompanyWideAccess()) {
            // Only filter by company, not project
            if (method_exists($this, 'getProjectIdColumn')) {
                $projectIds = \App\Models\Project::where('company_id', $user->company_id)->pluck('id');
                return $query->whereIn($this->getProjectIdColumn(), $projectIds);
            }
            return $query;
        }

        // Other roles: filter by assigned projects
        $accessibleProjectIds = $user->getAccessibleProjectIds();

        if (empty($accessibleProjectIds)) {
            return $query->whereRaw('1 = 0'); // No projects = no data
        }

        // Use the project_id column (can be overridden in model)
        $projectColumn = method_exists($this, 'getProjectIdColumn') 
            ? $this->getProjectIdColumn() 
            : 'project_id';

        return $query->whereIn($projectColumn, $accessibleProjectIds);
    }

    /**
     * Scope to filter by specific project with access check.
     * 
     * @param Builder $query
     * @param int $projectId
     * @param \App\Models\User|null $user
     * @return Builder
     */
    public function scopeForProject(Builder $query, int $projectId, $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Check if user has access to this project
        if (!$user->hasCompanyWideAccess() && !$user->hasProjectAccess($projectId)) {
            return $query->whereRaw('1 = 0');
        }

        $projectColumn = method_exists($this, 'getProjectIdColumn') 
            ? $this->getProjectIdColumn() 
            : 'project_id';

        return $query->where($projectColumn, $projectId);
    }

    /**
     * Get the column name for project_id.
     * Override this in model if column name is different.
     * 
     * @return string
     */
    public function getProjectIdColumn(): string
    {
        return 'project_id';
    }
}
