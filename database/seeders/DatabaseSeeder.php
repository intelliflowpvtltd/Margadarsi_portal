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
        // Call seeders in correct order respecting dependencies
        $this->call([
            PermissionSeeder::class,      // 1. Seed permissions first
            RoleSeeder::class,             // 2. Create roles with permissions
            UserSeeder::class,             // 3. Create users with roles
        ]);
    }
}
