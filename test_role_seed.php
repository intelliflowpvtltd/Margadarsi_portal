<?php

use App\Models\Company;
use App\Models\Role;

echo "Testing Role Seeding...\n\n";

try {
    $company = Company::where('email', 'admin@margadarsi.com')->first();
    
    if (!$company) {
        echo "❌ Company not found!\n";
        exit(1);
    }
    
    echo "✅ Company found: {$company->name} (ID: {$company->id})\n\n";
    
    echo "Testing SYSTEM_ROLES constant access...\n";
    $systemRoles = Role::SYSTEM_ROLES;
    echo "✅ SYSTEM_ROLES constant exists with " . count($systemRoles) . " roles\n\n";
    
    echo "Creating roles...\n";
    foreach ($systemRoles as $roleData) {
        echo "- Creating role: {$roleData['slug']}\n";
        $role = Role::firstOrCreate(
            [
                'company_id' => $company->id,
                'slug' => $roleData['slug'],
            ],
            [
                'name' => $roleData['name'],
                'description' => $roleData['description'],
                'hierarchy_level' => $roleData['hierarchy_level'],
                'is_system' => true,
                'is_active' => true,
            ]
        );
        echo "  ✅ Role ID: {$role->id}\n";
    }
    
    echo "\n✅ All roles created successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ FAILED!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Class: " . get_class($e) . "\n\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
