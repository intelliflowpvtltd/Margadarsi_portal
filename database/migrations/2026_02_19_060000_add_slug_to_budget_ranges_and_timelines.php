<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add slug and description columns to budget_ranges and timelines tables.
     * These columns are declared as fillable in the models (via IsMaster trait)
     * but were missing from the original migrations.
     */
    public function up(): void
    {
        Schema::table('budget_ranges', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->after('name');
            $table->text('description')->nullable()->after('currency');
            $table->unique('slug');
        });

        Schema::table('timelines', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->after('name');
            $table->text('description')->nullable()->after('max_days');
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('budget_ranges', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'description']);
        });

        Schema::table('timelines', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'description']);
        });
    }
};
