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
        Schema::create('specification_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('specification_categories')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->string('data_type', 20)->default('text'); // text, number, boolean, select, date
            $table->json('allowed_values')->nullable(); // For select type
            $table->string('unit', 50)->nullable(); // sq.ft, meters, etc.
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
            $table->unique(['category_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specification_types');
    }
};
