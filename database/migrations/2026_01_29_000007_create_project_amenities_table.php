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
        Schema::create('project_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            $table->string('name', 100);
            $table->enum('category', [
                'lifestyle',    // Club house, Banquet hall, Party lawn
                'sports',       // Swimming pool, Gym, Tennis court
                'convenience',  // Power backup, Elevator, Water supply
                'security',     // CCTV, Intercom, Gated entry
                'kids',         // Play area, Creche, Kids pool
                'health',       // Jogging track, Yoga deck, Spa
                'green',        // Gardens, Parks, Landscaping
                'other'
            ]);
            $table->string('icon')->nullable(); // Icon name or path
            $table->text('description')->nullable();
            $table->boolean('is_highlighted')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('project_id');
            $table->index('category');
            $table->index('is_highlighted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_amenities');
    }
};
