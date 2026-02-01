<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Role hierarchy and scope configuration.
     * Levels 1-3 (Super Admin, Admin, Sales Director) = company scope
     * Levels 4+ (Sales Manager and below) = project scope
     */
    private const COMPANY_LEVEL_THRESHOLD = 3;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing roles with correct scope based on hierarchy_level
        // Company-wide roles: Super Admin (L1), Admin (L2), Sales Director (L3)
        DB::table('roles')
            ->where('hierarchy_level', '<=', self::COMPANY_LEVEL_THRESHOLD)
            ->update(['scope' => 'company']);

        // Project-specific roles: Sales Manager (L4) and below
        DB::table('roles')
            ->where('hierarchy_level', '>', self::COMPANY_LEVEL_THRESHOLD)
            ->update(['scope' => 'project']);

        // Also set NULL scope to 'company' for any roles without scope
        DB::table('roles')
            ->whereNull('scope')
            ->update(['scope' => 'company']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all scopes to NULL (they'll be computed by the model accessor)
        DB::table('roles')
            ->update(['scope' => null]);
    }
};
