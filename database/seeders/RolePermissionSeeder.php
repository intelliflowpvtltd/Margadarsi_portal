<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{

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
            // Get permission names for this role slug from the Permission model matrix
            $permissionNames = Permission::ROLE_PERMISSION_MATRIX[$role->slug] ?? [];

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
