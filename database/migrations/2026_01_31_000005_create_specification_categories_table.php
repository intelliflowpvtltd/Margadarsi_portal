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
        Schema::create('specification_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->onDelete('set null');
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->string('icon', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['property_type_id', 'is_active']);
            $table->unique(['property_type_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specification_categories');
    }
};
