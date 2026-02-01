<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds company_id to departments table for company-level departments
     * like Management. Management department should be above project level and not
     * require project assignment.
     */
    public function up(): void
    {
        // Add company_id column if it doesn't exist
        if (!Schema::hasColumn('departments', 'company_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->onDelete('cascade');
            });
        }

        // Make project_id nullable if it isn't already
        // This allows Management departments to exist at company level without project
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->change();
        });

        // Update Management departments to be company-level
        // Set company_id from the project's company_id, then clear project_id
        $managementDepts = DB::table('departments')
            ->where('slug', 'management')
            ->whereNotNull('project_id')
            ->get();

        foreach ($managementDepts as $dept) {
            $project = DB::table('projects')->find($dept->project_id);
            if ($project) {
                DB::table('departments')
                    ->where('id', $dept->id)
                    ->update([
                        'company_id' => $project->company_id,
                        'project_id' => null, // Management is above project level
                    ]);
            }
        }

        // For any departments that still have project_id but no company_id,
        // set company_id from their project
        $depts = DB::table('departments')
            ->whereNull('company_id')
            ->whereNotNull('project_id')
            ->get();

        foreach ($depts as $dept) {
            $project = DB::table('projects')->find($dept->project_id);
            if ($project) {
                DB::table('departments')
                    ->where('id', $dept->id)
                    ->update(['company_id' => $project->company_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore project_id for Management departments from companies' first project
        $managementDepts = DB::table('departments')
            ->where('slug', 'management')
            ->whereNull('project_id')
            ->get();

        foreach ($managementDepts as $dept) {
            $firstProject = DB::table('projects')
                ->where('company_id', $dept->company_id)
                ->first();
            if ($firstProject) {
                DB::table('departments')
                    ->where('id', $dept->id)
                    ->update(['project_id' => $firstProject->id]);
            }
        }

        // Remove company_id column
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
