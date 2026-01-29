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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('tagline', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Registration (India-specific)
            $table->string('pan_number', 10)->nullable();
            $table->string('gstin', 15)->nullable();
            $table->string('cin', 21)->nullable();
            $table->string('rera_number', 50)->nullable();
            $table->date('incorporation_date')->nullable();

            // Contact Information
            $table->string('email')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('alternate_phone', 15)->nullable();
            $table->string('whatsapp', 15)->nullable();
            $table->string('website')->nullable();

            // Address - Registered Office
            $table->string('registered_address_line1')->nullable();
            $table->string('registered_address_line2')->nullable();
            $table->string('registered_city', 100)->nullable();
            $table->string('registered_state', 100)->nullable();
            $table->string('registered_pincode', 6)->nullable();
            $table->string('registered_country', 100)->default('India');

            // Address - Corporate/Branch Office
            $table->string('corporate_address_line1')->nullable();
            $table->string('corporate_address_line2')->nullable();
            $table->string('corporate_city', 100)->nullable();
            $table->string('corporate_state', 100)->nullable();
            $table->string('corporate_pincode', 6)->nullable();

            // Social Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('youtube_url')->nullable();

            // Status & Timestamps
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
