<?php

namespace App\Traits;

use App\Models\Role;

trait HasPermissions
{
    /**
     * Cached permissions array.
     * @var array|null
     */
    protected $cachedPermissions = null;

    /**
     * Load and cache user permissions.
     * This method loads permissions once and caches them for the request lifecycle.
     *
     * @return array
     */
    protected function loadPermissions(): array
    {
        // Return cached if already loaded
        if ($this->cachedPermissions !== null) {
            return $this->cachedPermissions;
        }

        // No role = no permissions
        if (!$this->role) {
            $this->cachedPermissions = [];
            return [];
        }

        // Load permissions from database and cache
        $this->cachedPermissions = $this->role->permissions()
            ->pluck('name')
            ->toArray();

        return $this->cachedPermissions;
    }

    /**
     * Clear cached permissions.
     * Should be called when user's role changes.
     *
     * @return void
     */
    public function clearPermissionCache(): void
    {
        $this->cachedPermissions = null;
    }

    /**
     * Check if user has a specific permission.
     * Uses in-memory cache to avoid DB queries.
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(string $permissionName): bool
    {
        $permissions = $this->loadPermissions();
        return in_array($permissionName, $permissions);
    }

    /**
     * Check if user has any of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        $userPermissions = $this->loadPermissions();
        
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has all of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions): bool
    {
        $userPermissions = $this->loadPermissions();
        
        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get all permission names for this user.
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->loadPermissions();
    }

    /**
     * Check if user's role is a system role.
     *
     * @return bool
     */
    public function hasSystemRole(): bool
    {
        return $this->role && $this->role->is_system;
    }

    /**
     * Check if user is super admin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->slug === 'super_admin';
    }
}
