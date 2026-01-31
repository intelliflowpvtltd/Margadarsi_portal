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
        Schema::create('property_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->onDelete('set null');
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('color_code', 7)->nullable();
            $table->string('badge_class', 50)->nullable();
            $table->smallInteger('workflow_order')->default(0);
            $table->boolean('is_final_state')->default(false);
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index(['property_type_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_statuses');
    }
};
