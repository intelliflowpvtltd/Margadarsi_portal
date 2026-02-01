<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('users', 'employee_code')) {
                $table->string('employee_code', 50)->nullable()->after('avatar');
            }
            
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation', 100)->nullable()->after('employee_code');
            }
            
            if (!Schema::hasColumn('users', 'reports_to')) {
                $table->foreignId('reports_to')->nullable()->after('designation')
                    ->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'reporting_path')) {
                $table->text('reporting_path')->nullable()->after('reports_to');
            }
        });

        // Add indexes separately to handle potential existing indexes
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_code')) {
                // Indexes already added above
                return;
            }
            
            // Try to add indexes if they don't exist
            try {
                $table->index('employee_code');
            } catch (\Exception $e) {
                // Index may already exist
            }
            
            try {
                $table->index('reports_to');
            } catch (\Exception $e) {
                // Index may already exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first if exists
            if (Schema::hasColumn('users', 'reports_to')) {
                try {
                    $table->dropForeign(['reports_to']);
                } catch (\Exception $e) {
                    // FK may not exist
                }
            }
            
            // Drop columns if they exist
            $columnsToDrop = [];
            foreach (['employee_code', 'designation', 'reports_to', 'reporting_path'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
