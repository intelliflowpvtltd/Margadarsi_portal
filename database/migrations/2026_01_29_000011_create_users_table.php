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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Company, Role, and Department relationships
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('restrict');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');

            // Personal Information
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Contact Information
            $table->string('phone', 20)->nullable();

            // Profile
            $table->string('avatar')->nullable();

            // Status and Activity
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            // Laravel defaults
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['company_id', 'email']); // Email unique per company
            $table->index('role_id');
            $table->index('department_id');
            $table->index('is_active');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
