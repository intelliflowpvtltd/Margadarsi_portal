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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Relationship to project
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // Department details
            $table->string('name', 100); // Management, Sales, Pre-Sales
            $table->string('slug', 100);
            $table->text('description')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Ensure unique department slug per project
            $table->unique(['project_id', 'slug']);

            // Indexes
            $table->index('project_id');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
