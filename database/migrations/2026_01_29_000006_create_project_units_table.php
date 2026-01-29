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
        Schema::create('project_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // Unit Identification
            $table->string('name', 100); // 2BHK Type A, 3BHK Premium, Shop 01
            $table->enum('type', [
                '1bhk',
                '2bhk',
                '3bhk',
                '4bhk',
                '5bhk',
                'studio',
                'penthouse',
                'duplex',
                'shop',
                'office',
                'showroom',
                'plot',
                'villa'
            ]);

            // Area Details
            $table->decimal('carpet_area_sqft', 10, 2)->nullable();
            $table->decimal('built_up_area_sqft', 10, 2)->nullable();
            $table->decimal('super_built_up_sqft', 10, 2)->nullable();
            $table->decimal('plot_area_sqyds', 10, 2)->nullable(); // For plots/villas

            // Configuration
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('balconies')->default(0);
            $table->string('facing', 50)->nullable(); // East, West, North, South, NE, NW, SE, SW

            // Visuals
            $table->string('floor_plan_image')->nullable();
            $table->string('3d_view_image')->nullable();

            // Pricing
            $table->boolean('price_on_request')->default(true);
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('price_per_sqft', 10, 2)->nullable();

            // Availability
            $table->integer('total_units')->default(0);
            $table->integer('available_units')->default(0);
            $table->integer('booked_units')->default(0);

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('project_id');
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_units');
    }
};
