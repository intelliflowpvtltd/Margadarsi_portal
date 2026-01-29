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
        Schema::create('project_towers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            $table->string('name', 100); // Tower A, Block 1, Wing A
            $table->integer('total_floors')->nullable();
            $table->integer('units_per_floor')->nullable();
            $table->integer('basement_levels')->default(0);
            $table->boolean('has_terrace')->default(false);

            $table->enum('status', ['upcoming', 'construction', 'completed'])->default('upcoming');
            $table->date('completion_date')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('project_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_towers');
    }
};
