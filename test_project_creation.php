<?php

use App\Models\Company;
use App\Models\Project;

echo "Testing Project Creation...\n\n";

// Get company
$company = Company::where('email', 'admin@margadarsi.com')->first();

if (!$company) {
    echo "❌ Company not found\n";
    exit(1);
}

echo "✅ Company found: {$company->name}\n";
echo "Company ID: {$company->id}\n\n";

// Test exact same code from seeder
try {
    echo "Attempting to create project...\n";
    
    $project = Project::firstOrCreate(
        [
            'company_id' => $company->id,
            'slug' => 'margadarsi-heights',
        ],
        [
            'name' => 'Margadarsi Heights',
            'type' => 'residential',
            'status' => 'ongoing',
            'description' => 'Premium residential apartments in the heart of Hyderabad',
            'address_line1' => 'Survey No. 45, Gachibowli',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
            'pincode' => '500081',
            'is_featured' => true,
        ]
    );
    
    echo "✅ SUCCESS! Project created\n";
    echo "Project ID: {$project->id}\n";
    echo "Project Name: {$project->name}\n";
    
} catch (\Exception $e) {
    echo "❌ FAILED!\n\n";
    echo "Error Message:\n";
    echo $e->getMessage() . "\n\n";
    echo "Error Class:\n";
    echo get_class($e) . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
