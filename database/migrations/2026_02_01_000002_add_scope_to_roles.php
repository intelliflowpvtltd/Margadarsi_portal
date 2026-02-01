<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Add explicit scope enum column
            $table->enum('scope', ['company', 'project', 'department'])
                ->default('company')
                ->after('department_id');
            $table->index('scope');
        });

        // Populate existing data based on foreign keys
        DB::statement("
            UPDATE roles SET scope = CASE
                WHEN department_id IS NOT NULL AND project_id IS NOT NULL THEN 'project'
                WHEN department_id IS NOT NULL AND project_id IS NULL THEN 'department'
                ELSE 'company'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['scope']);
            $table->dropColumn('scope');
        });
    }
};
