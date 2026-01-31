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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('team_role', 30)->default('member'); // member, lead, manager
            $table->integer('assignment_weight')->default(1); // For weighted round-robin
            $table->integer('max_active_leads')->default(50);
            $table->integer('current_active_leads')->default(0);
            $table->boolean('is_available')->default(true);
            $table->time('available_from')->nullable();
            $table->time('available_to')->nullable();
            $table->json('working_days')->nullable(); // [1,2,3,4,5] for Mon-Fri
            $table->timestamp('last_assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
            $table->index(['team_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
