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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            $table->string('name', 100);
            $table->string('slug', 100);
            $table->text('description')->nullable();

            // Hierarchy: lower number = higher authority
            // 1 = Super Admin, 2 = Admin, 3 = Sales Manager, etc.
            $table->unsignedTinyInteger('hierarchy_level')->default(99);

            // System roles cannot be deleted
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: no duplicate slugs within same company
            $table->unique(['company_id', 'slug']);

            // Indexes
            $table->index('company_id');
            $table->index('hierarchy_level');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
