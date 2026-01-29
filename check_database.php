<?php

use Illuminate\Support\Facades\DB;

$company_count = DB::table('companies')->count();
$project_count = DB::table('projects')->count();
$role_count = DB::table('roles')->count();
$user_count = DB::table('users')->count();
$permission_count = DB::table('permissions')->count();
$role_permission_count = DB::table('role_permissions')->count();

echo "=== DATABASE SEEDING STATUS ===\n\n";
echo "Companies: {$company_count}\n";
echo "Projects: {$project_count}\n";
echo "Roles: {$role_count}\n";
echo "Users: {$user_count}\n";
echo "Permissions: {$permission_count}\n";
echo "Role-Permissions: {$role_permission_count}\n\n";

if ($permission_count > 0) {
    echo "=== ALL PERMISSIONS ===\n";
    $permissions = DB::table('permissions')->orderBy('category')->orderBy('name')->get(['name', 'category']);
    foreach ($permissions as $perm) {
        echo "- {$perm->category}: {$perm->name}\n";
    }
    echo "\n";
}

if ($company_count > 0) {
    echo "=== COMPANIES ===\n";
    $companies = DB::table('companies')->get(['id', 'name', 'email']);
    foreach ($companies as $comp) {
        echo "#{$comp->id}: {$comp->name} ({$comp->email})\n";
    }
    echo "\n";
}

if ($role_count > 0) {
    echo "=== ROLES WITH PERMISSIONS ===\n";
    $roles = DB::table('roles')->orderBy('hierarchy_level')->get();
    foreach ($roles as $role) {
        $perm_count = DB::table('role_permissions')->where('role_id', $role->id)->count();
        echo "#{$role->id}: {$role->name} (Level {$role->hierarchy_level}) - {$perm_count} permissions\n";

        $perms = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role_id', $role->id)
            ->pluck('permissions.name')
            ->toArray();

        if (count($perms) > 0) {
            echo "  Permissions: " . implode(', ', $perms) . "\n";
        }
    }
}

echo "\n=== CRUD COVERAGE CHECK ===\n";
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

    $status = array_filter($crud_perms) ? '✓' : '✗';
    $count = count(array_filter($crud_perms));
    echo "{$status} {$module}: {$count}/6 CRUD permissions\n";

    foreach ($crud_perms as $action => $exists) {
        echo "  " . ($exists ? '✓' : '✗') . " {$module}.{$action}\n";
    }
}
