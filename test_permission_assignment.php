<?php

use App\Models\Permission;
use App\Models\Company;
use App\Models\Role;

echo "Testing Permission Assignment to Roles...\n\n";

$company = Company::where('email', 'admin@margadarsi.com')->first();
$roles = Role::where('company_id', $company->id)->get()->keyBy('slug');

echo "Found " . $roles->count() . " roles\n";
echo "Roles: " . $roles->pluck('slug')->implode(', ') . "\n\n";

echo "Checking ROLE_PERMISSION_MATRIX...\n";
if (!defined('App\\Models\\Permission::ROLE_PERMISSION_MATRIX')) {
    echo "Checking if constant exists...\n";
}

try {
    $matrix = Permission::ROLE_PERMISSION_MATRIX;
    echo "✅ ROLE_PERMISSION_MATRIX exists with " . count($matrix) . " role entries\n\n";
    
    echo "Matrix keys: " . implode(', ', array_keys($matrix)) . "\n\n";
    
    $permissions = Permission::all()->keyBy('name');
    echo "Total permissions in DB: " . $permissions->count() . "\n\n";
    
    foreach ($roles as $slug => $role) {
        echo "Processing role: {$slug}\n";
        $permissionNames = $matrix[$slug] ?? [];
        echo "  Permissions to assign: " . count($permissionNames) . "\n";
        
        $permissionIds = $permissions->whereIn('name', $permissionNames)->pluck('id');
        echo "  Found permission IDs: " . $permissionIds->count() . "\n";
        
        $role->permissions()->sync($permissionIds);
        echo "  ✅ Synced\n\n";
    }
    
    echo "✅ All permissions assigned successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ FAILED!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Class: " . get_class($e) . "\n\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n\n";
}
