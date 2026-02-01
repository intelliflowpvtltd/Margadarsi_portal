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
            // Add materialized path column for efficient hierarchical queries
            $table->string('reporting_path', 500)->nullable()->after('reports_to');
            $table->index('reporting_path');
        });

        // Populate existing reporting paths
        $this->populateReportingPaths();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['reporting_path']);
            $table->dropColumn('reporting_path');
        });
    }

    /**
     * Populate reporting paths for existing users.
     */
    private function populateReportingPaths(): void
    {
        // Get all users with their reporting relationships
        $users = \App\Models\User::whereNotNull('reports_to')
            ->orderBy('reports_to')
            ->get();

        foreach ($users as $user) {
            $this->buildReportingPath($user);
        }
    }

    /**
     * Recursively build reporting path for a user.
     */
    private function buildReportingPath($user): string
    {
        if (!$user->reports_to) {
            $user->reporting_path = null;
            $user->save();
            return '';
        }

        $manager = \App\Models\User::find($user->reports_to);
        
        if (!$manager) {
            $user->reporting_path = (string) $user->reports_to;
            $user->save();
            return (string) $user->reports_to;
        }

        // Get manager's path (recursive)
        $managerPath = $manager->reporting_path ?: $this->buildReportingPath($manager);
        
        // Build user's path
        $user->reporting_path = $managerPath 
            ? $managerPath . '/' . $user->reports_to 
            : (string) $user->reports_to;
        
        $user->save();
        
        return $user->reporting_path;
    }
};
