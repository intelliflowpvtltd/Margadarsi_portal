<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabaseSeeding extends Command
{
    protected $signature = 'db:check-seeding';
    protected $description = 'Check database seeding status and CRUD permissions';

    public function handle()
    {
        $company_count = DB::table('companies')->count();
        $project_count = DB::table('projects')->count();
        $role_count = DB::table('roles')->count();
        $user_count = DB::table('users')->count();
        $permission_count = DB::table('permissions')->count();
        $role_permission_count = DB::table('role_permissions')->count();

        $this->info('=== DATABASE SEEDING STATUS ===');
        $this->newLine();
        $this->line("Companies: {$company_count}");
        $this->line("Projects: {$project_count}");
        $this->line("Roles: {$role_count}");
        $this->line("Users: {$user_count}");
        $this->line("Permissions: {$permission_count}");
        $this->line("Role-Permissions: {$role_permission_count}");
        $this->newLine();

        if ($permission_count > 0) {
            $this->info('=== ALL PERMISSIONS ===');
            $permissions = DB::table('permissions')->orderBy('name')->get(['name']);
            foreach ($permissions as $perm) {
                $this->line("- {$perm->name}");
            }
            $this->newLine();
        }

        if ($company_count > 0) {
            $this->info('=== COMPANIES ===');
            $companies = DB::table('companies')->get(['id', 'name', 'email']);
            foreach ($companies as $comp) {
                $this->line("#{$comp->id}: {$comp->name} ({$comp->email})");
            }
            $this->newLine();
        }

        if ($role_count > 0) {
            $this->info('=== ROLES WITH PERMISSIONS ===');
            $roles = DB::table('roles')->orderBy('hierarchy_level')->get();
            foreach ($roles as $role) {
                $perm_count = DB::table('role_permissions')->where('role_id', $role->id)->count();
                $this->line("#{$role->id}: {$role->name} (Level {$role->hierarchy_level}) - {$perm_count} permissions");

                $perms = DB::table('role_permissions')
                    ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                    ->where('role_permissions.role_id', $role->id)
                    ->pluck('permissions.name')
                    ->toArray();

                if (count($perms) > 0 && count($perms) <= 15) {
                    $this->line("  Permissions: " . implode(', ', $perms));
                } elseif (count($perms) > 15) {
                    $this->line("  Permissions: " . count($perms) . " total");
                }
            }
            $this->newLine();
        }

        $this->info('=== CRUD COVERAGE CHECK ===');
        $modules = ['companies', 'projects', 'roles', 'users'];
        foreach ($modules as $module) {
            $crud_perms = [
                'view' => DB::table('permissions')->where('name', "{$module}.view")->exists(),
                'create' => DB::table('permissions')->where('name', "{$module}.create")->exists(),
                'update' => DB::table('permissions')->where('name', "{$module}.update")->exists(),
                'delete' => DB::table('permissions')->where('name', "{$module}.delete")->exists(),
                'restore' => DB::table('permissions')->where('name', "{$module}.restore")->exists(),
                'force-delete' => DB::table('permissions')->where('name', "{$module}.force-delete")->exists(),
            ];

            $count = count(array_filter($crud_perms));
            $status = $count === 6 ? '✓' : ($count > 0 ? '⚠' : '✗');
            $this->line("{$status} {$module}: {$count}/6 CRUD permissions");

            foreach ($crud_perms as $action => $exists) {
                $icon = $exists ? '✓' : '✗';
                $this->line("  {$icon} {$module}.{$action}");
            }
        }

        $this->newLine();

        if ($company_count === 0 || $permission_count === 0) {
            $this->warn('⚠️  Database appears to be empty. Run: php artisan db:seed --class=UserSeeder');
        } else {
            $this->info('✅ Database is seeded!');
        }

        return 0;
    }
}
