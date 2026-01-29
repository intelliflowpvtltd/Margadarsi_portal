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
        // Residential Specifications (Apartments/Towers)
        Schema::create('residential_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->onDelete('cascade');

            // Tower Details
            $table->integer('total_towers')->default(1);
            $table->integer('total_floors_per_tower')->nullable();
            $table->integer('total_units')->nullable();
            $table->integer('units_per_floor')->nullable();

            // Parking
            $table->integer('basement_levels')->default(0);
            $table->boolean('stilt_parking')->default(false);
            $table->integer('open_parking_slots')->default(0);
            $table->integer('covered_parking_slots')->default(0);

            // Clubhouse
            $table->integer('clubhouse_floors')->default(0);
            $table->decimal('clubhouse_area_sqft', 10, 2)->nullable();

            // Podium
            $table->boolean('podium_level')->default(false);
            $table->decimal('podium_area_sqft', 10, 2)->nullable();

            $table->timestamps();
        });

        // Commercial Specifications
        Schema::create('commercial_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->onDelete('cascade');

            // Building Details
            $table->integer('total_floors')->nullable();
            $table->integer('total_units')->nullable();
            $table->integer('office_units')->default(0);
            $table->integer('retail_units')->default(0);

            // Common Areas
            $table->decimal('food_court_area_sqft', 10, 2)->nullable();
            $table->decimal('common_area_percentage', 5, 2)->nullable();

            // Parking
            $table->integer('basement_levels')->default(0);
            $table->integer('visitor_parking_slots')->default(0);
            $table->integer('tenant_parking_slots')->default(0);

            // Certifications
            $table->boolean('green_building_certified')->default(false);
            $table->string('certification_type', 50)->nullable(); // LEED, IGBC, GRIHA

            $table->timestamps();
        });

        // Villa Specifications
        Schema::create('villa_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->onDelete('cascade');

            // Villa Details
            $table->integer('total_villas')->nullable();
            $table->integer('villa_types')->default(1);
            $table->integer('floors_per_villa')->default(2);

            // Sizes (JSON arrays for multiple sizes)
            $table->json('plot_sizes_sqft')->nullable();
            $table->json('built_up_sizes_sqft')->nullable();

            // Features
            $table->boolean('private_garden')->default(true);
            $table->boolean('private_pool')->default(false);
            $table->boolean('servant_quarters')->default(false);
            $table->integer('car_parking_per_villa')->default(2);

            // Community
            $table->boolean('gated_community')->default(true);
            $table->decimal('clubhouse_area_sqft', 10, 2)->nullable();

            $table->timestamps();
        });

        // Open Plot Specifications
        Schema::create('open_plot_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->onDelete('cascade');

            // Plot Details
            $table->integer('total_plots')->nullable();
            $table->json('plot_sizes')->nullable(); // Array of available sizes
            $table->decimal('min_plot_size_sqyds', 10, 2)->nullable();
            $table->decimal('max_plot_size_sqyds', 10, 2)->nullable();

            // Infrastructure
            $table->json('road_width_feet')->nullable(); // [30, 40, 60]
            $table->boolean('underground_drainage')->default(false);
            $table->boolean('underground_electricity')->default(false);
            $table->boolean('water_supply')->default(false);

            // Boundaries
            $table->boolean('compound_wall')->default(false);
            $table->boolean('avenue_plantation')->default(false);
            $table->boolean('fencing')->default(false);

            // Common Areas
            $table->decimal('park_area_sqft', 10, 2)->nullable();
            $table->decimal('community_hall_sqft', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_plot_specs');
        Schema::dropIfExists('villa_specs');
        Schema::dropIfExists('commercial_specs');
        Schema::dropIfExists('residential_specs');
    }
};
