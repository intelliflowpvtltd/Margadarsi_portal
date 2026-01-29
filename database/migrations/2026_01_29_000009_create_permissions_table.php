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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            // Permission identifier (e.g., 'companies.create')
            $table->string('name', 100)->unique();

            // Human-readable name
            $table->string('display_name', 100);

            // Description of what this permission allows
            $table->text('description')->nullable();

            // Module grouping (companies, projects, roles, etc.)
            $table->string('module', 50);

            $table->timestamps();

            // Indexes
            $table->index('module');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
