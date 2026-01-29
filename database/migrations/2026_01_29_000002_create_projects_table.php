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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            // Basic Details
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->enum('type', ['residential', 'commercial', 'villa', 'open_plots']);
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'sold_out'])->default('upcoming');
            $table->text('description')->nullable();
            $table->json('highlights')->nullable();

            // RERA Details
            $table->string('rera_number', 50)->nullable();
            $table->date('rera_valid_until')->nullable();

            // Location
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('landmark')->nullable();
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('pincode', 6)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('google_maps_url')->nullable();

            // Land Details
            $table->decimal('total_land_area', 10, 2)->nullable();
            $table->enum('land_area_unit', ['acres', 'sqft', 'sqm', 'sqyds'])->default('acres');

            // Timeline
            $table->date('launch_date')->nullable();
            $table->date('possession_date')->nullable();
            $table->tinyInteger('completion_percentage')->default(0);

            // Meta
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('type');
            $table->index('status');
            $table->index('city');
            $table->index('is_featured');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
