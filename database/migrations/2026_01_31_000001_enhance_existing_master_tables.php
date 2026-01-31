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
        // Enhance countries table
        Schema::table('countries', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->after('code');
            $table->string('icon', 50)->nullable()->after('slug');
        });

        // Enhance property_types table
        Schema::table('property_types', function (Blueprint $table) {
            $table->string('icon', 50)->nullable()->after('slug');
            $table->string('color_code', 7)->nullable()->after('icon');
        });

        // Enhance cities table
        Schema::table('cities', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('state_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->boolean('is_metro')->default(false)->after('longitude');
            $table->boolean('is_tier1')->default(false)->after('is_metro');
            $table->boolean('is_tier2')->default(false)->after('is_tier1');
            $table->boolean('is_tier3')->default(false)->after('is_tier2');
        });

        // Enhance lead_statuses table
        Schema::table('lead_statuses', function (Blueprint $table) {
            $table->string('color_code', 7)->nullable()->after('description');
            $table->string('badge_class', 50)->nullable()->after('color_code');
            $table->smallInteger('workflow_order')->nullable()->after('badge_class');
            $table->boolean('is_pipeline_state')->default(true)->after('workflow_order');
            $table->boolean('is_final_state')->default(false)->after('is_pipeline_state');
        });

        // Enhance lead_sources table
        Schema::table('lead_sources', function (Blueprint $table) {
            $table->string('icon', 50)->nullable()->after('slug');
            $table->string('color_code', 7)->nullable()->after('icon');
        });

        // Enhance states table
        Schema::table('states', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['slug', 'icon']);
        });

        Schema::table('property_types', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color_code']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'is_metro', 'is_tier1', 'is_tier2', 'is_tier3']);
        });

        Schema::table('lead_statuses', function (Blueprint $table) {
            $table->dropColumn(['color_code', 'badge_class', 'workflow_order', 'is_pipeline_state', 'is_final_state']);
        });

        Schema::table('lead_sources', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color_code']);
        });

        Schema::table('states', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
