<?php

use App\Models\Permission;

echo "Testing Permission Seeding...\n\n";

try {
    echo "Current permission count: " . Permission::count() . "\n";
    
    if (Permission::count() === 0) {
        echo "Seeding permissions...\n";
        Permission::seedPermissions();
        echo "✅ Permissions seeded successfully!\n";
    } else {
        echo "✅ Permissions already exist\n";
    }
    
    echo "\nFinal permission count: " . Permission::count() . "\n";
    
} catch (\Exception $e) {
    echo "❌ FAILED!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Class: " . get_class($e) . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
