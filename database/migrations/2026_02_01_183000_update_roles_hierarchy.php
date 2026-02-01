<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update existing roles with correct hierarchy levels and scopes.
     * 
     * CORRECT HIERARCHY:
     * Level 1: Super Admin (company scope) - Father of System
     * Level 2: Company Admin (company scope) - Father of Company  
     * Level 3: Project Manager (project scope) - Father of Project
     * Level 4: Senior Sales Executive, Team Leader (project scope)
     * Level 5: Sales Executive, Telecaller (project scope)
     * Level 6: Channel Partner (project scope)
     */
    public function up(): void
    {
        // Update Super Admin
        DB::table('roles')
            ->where('slug', 'super_admin')
            ->update([
                'name' => 'Super Admin',
                'description' => 'Father of System - Full access across all companies and projects',
                'hierarchy_level' => 1,
                'scope' => 'company',
            ]);

        // Update Admin -> Company Admin
        DB::table('roles')
            ->where('slug', 'admin')
            ->update([
                'name' => 'Company Admin',
                'slug' => 'company_admin',
                'description' => 'Father of Company - Full access to all projects within the company',
                'hierarchy_level' => 2,
                'scope' => 'company',
            ]);

        // Delete old roles that don't exist in new hierarchy
        DB::table('roles')
            ->whereIn('slug', ['sales_director', 'sales_manager'])
            ->delete();

        // Update Project Manager
        DB::table('roles')
            ->where('slug', 'project_manager')
            ->update([
                'description' => 'Father of Project - Manages project operations and team',
                'hierarchy_level' => 3,
                'scope' => 'project',
            ]);

        // Update Senior Sales Executive
        DB::table('roles')
            ->where('slug', 'senior_sales_executive')
            ->update([
                'description' => 'Senior sales role within assigned projects',
                'hierarchy_level' => 4,
                'scope' => 'project',
            ]);

        // Update Sales Executive
        DB::table('roles')
            ->where('slug', 'sales_executive')
            ->update([
                'description' => 'Handles sales leads and conversions for assigned projects',
                'hierarchy_level' => 5,
                'scope' => 'project',
            ]);

        // Update Team Lead -> Team Leader
        DB::table('roles')
            ->where('slug', 'team_lead')
            ->update([
                'name' => 'Team Leader',
                'slug' => 'team_leader',
                'description' => 'Leads pre-sales team within assigned projects',
                'hierarchy_level' => 4,
                'scope' => 'project',
            ]);

        // Update Telecaller
        DB::table('roles')
            ->where('slug', 'telecaller')
            ->update([
                'description' => 'Handles initial lead calls and qualification for assigned projects',
                'hierarchy_level' => 5,
                'scope' => 'project',
            ]);

        // Update Channel Partner
        DB::table('roles')
            ->where('slug', 'channel_partner')
            ->update([
                'description' => 'External lead source with limited access to assigned projects',
                'hierarchy_level' => 6,
                'scope' => 'project',
            ]);

        // Update all remaining company-level roles (hierarchy_level <= 2)
        DB::table('roles')
            ->where('hierarchy_level', '<=', 2)
            ->update(['scope' => 'company']);

        // Update all remaining project-level roles (hierarchy_level > 2)
        DB::table('roles')
            ->where('hierarchy_level', '>', 2)
            ->update(['scope' => 'project']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Company Admin back to Admin
        DB::table('roles')
            ->where('slug', 'company_admin')
            ->update([
                'name' => 'Admin',
                'slug' => 'admin',
            ]);

        // Revert Team Leader back to Team Lead
        DB::table('roles')
            ->where('slug', 'team_leader')
            ->update([
                'name' => 'Team Lead',
                'slug' => 'team_lead',
            ]);
    }
};
