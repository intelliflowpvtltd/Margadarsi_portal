<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Permission mappings for each role slug.
     */
    private const ROLE_PERMISSIONS = [
        'super-admin' => [
            // Companies - Full access
            'companies.view',
            'companies.create',
            'companies.update',
            'companies.delete',
            'companies.restore',
            'companies.force-delete',
            // Projects - Full access
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
            'projects.restore',
            'projects.force-delete',
            'projects.manage-specifications',
            // Roles - Full access
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'roles.restore',
            'roles.seed',
        ],
        'admin' => [
            // Companies - All except force-delete
            'companies.view',
            'companies.create',
            'companies.update',
            'companies.delete',
            'companies.restore',
            // Projects - All except force-delete
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
            'projects.restore',
            'projects.manage-specifications',
            // Roles - All except force-delete (roles don't have force-delete)
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'roles.restore',
            'roles.seed',
        ],
        'sales-manager' => [
            // Companies - View only
            'companies.view',
            // Projects - Full management
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
            'projects.restore',
            'projects.manage-specifications',
            // Roles - View only
            'roles.view',
        ],
        'senior-sales-executive' => [
            // Companies - View only
            'companies.view',
            // Projects - View, update, manage specs
            'projects.view',
            'projects.update',
            'projects.manage-specifications',
            // Roles - View only
            'roles.view',
        ],
        'sales-executive' => [
            // Companies - View only
            'companies.view',
            // Projects - View only
            'projects.view',
            // Roles - View only
            'roles.view',
        ],
        'team-leader' => [
            // Companies - View only
            'companies.view',
            // Projects - View only
            'projects.view',
            // Roles - View only
            'roles.view',
        ],
        'tele-caller' => [
            // Companies - View only
            'companies.view',
            // Projects - View only
            'projects.view',
            // Roles - View only
            'roles.view',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles and permissions
        $roles = Role::all();
        $permissions = Permission::all()->keyBy('name');

        $assignedCount = 0;

        foreach ($roles as $role) {
            // Get permission names for this role slug
            $permissionNames = self::ROLE_PERMISSIONS[$role->slug] ?? [];

            if (empty($permissionNames)) {
                $this->command->warn("No permissions defined for role: {$role->name}");
                continue;
            }

            // Get permission IDs
            $permissionIds = [];
            foreach ($permissionNames as $permissionName) {
                if (isset($permissions[$permissionName])) {
                    $permissionIds[] = $permissions[$permissionName]->id;
                } else {
                    $this->command->warn("Permission '{$permissionName}' not found for role {$role->name}");
                }
            }

            // Sync permissions to role
            $role->permissions()->sync($permissionIds);
            $assignedCount += count($permissionIds);

            $this->command->info("Assigned {$role->permissions()->count()} permissions to role: {$role->name}");
        }

        $this->command->info("Total permissions assigned: {$assignedCount}");
    }
}
