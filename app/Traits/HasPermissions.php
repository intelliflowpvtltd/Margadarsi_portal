<?php

namespace App\Traits;

use App\Models\Role;

trait HasPermissions
{
    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permission names for this user.
     */
    public function getPermissions(): array
    {
        if (!$this->role) {
            return [];
        }

        return $this->role->permissions()
            ->pluck('name')
            ->toArray();
    }

    /**
     * Check if user's role is a system role.
     */
    public function hasSystemRole(): bool
    {
        return $this->role && $this->role->is_system;
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->slug === 'super-admin';
    }
}
