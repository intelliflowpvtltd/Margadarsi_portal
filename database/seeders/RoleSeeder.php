<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies and create system roles for each
        $companies = Company::all();

        foreach ($companies as $company) {
            // Check if company already has roles
            if ($company->roles()->count() === 0) {
                Role::createSystemRolesForCompany($company->id);
                $this->command->info("Created system roles for company: {$company->name}");
            } else {
                $this->command->info("Company '{$company->name}' already has roles, skipping...");
            }
        }

        if ($companies->isEmpty()) {
            $this->command->warn('No companies found. Create a company first, then run this seeder.');
        }
    }
}
