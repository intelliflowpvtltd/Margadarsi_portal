<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Use OrganizationSeeder which creates:
        // 1. Company
        // 2. Permissions
        // 3. Projects
        // 4. Departments (Management, Sales, Pre-Sales)
        // 5. Roles (properly associated with departments)
        // 6. Permissions assigned to roles
        // 7. Users with proper role assignments
        $this->call([
            OrganizationSeeder::class,
        ]);
    }
}
