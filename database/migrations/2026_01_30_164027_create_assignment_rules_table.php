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
        Schema::create('assignment_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->integer('priority')->default(100); // Lower = higher priority
            
            // Conditions (nullable = any)
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->foreignId('source_category_id')->nullable()->constrained('source_categories')->nullOnDelete();
            $table->foreignId('budget_range_id')->nullable()->constrained('budget_ranges')->nullOnDelete();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->json('pincodes')->nullable(); // Array of pincodes
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->foreignId('cp_user_id')->nullable()->constrained('users')->nullOnDelete(); // CP-specific rule
            
            // Target
            $table->foreignId('assign_to_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('assign_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'is_active', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_rules');
    }
};
