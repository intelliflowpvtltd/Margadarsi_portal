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
        Schema::table('user_projects', function (Blueprint $table) {
            $table->boolean('is_available_for_leads')->default(true)->after('access_level');
            $table->integer('max_active_leads')->default(50)->after('is_available_for_leads');
            $table->integer('current_active_leads')->default(0)->after('max_active_leads');
            $table->integer('assignment_weight')->default(1)->after('current_active_leads');
            $table->timestamp('last_lead_assigned_at')->nullable()->after('assignment_weight');
            
            $table->index(['project_id', 'is_available_for_leads']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_projects', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'is_available_for_leads']);
            $table->dropColumn([
                'is_available_for_leads',
                'max_active_leads', 
                'current_active_leads',
                'assignment_weight',
                'last_lead_assigned_at'
            ]);
        });
    }
};
