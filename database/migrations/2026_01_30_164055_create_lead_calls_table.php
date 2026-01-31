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
        Schema::create('lead_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Call Details
            $table->string('attempt_outcome', 20); // connected, not_answering, busy, switched_off, wrong_number, callback_requested, not_reachable
            $table->string('call_outcome', 20)->nullable(); // qualified, not_qualified (only if connected)
            $table->integer('duration_seconds')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            
            // If Connected
            $table->text('summary')->nullable();
            $table->string('recording_url', 500)->nullable();
            $table->foreignId('temperature_tag_id')->nullable()->constrained('temperature_tags')->nullOnDelete();
            $table->timestamp('next_followup_at')->nullable();
            $table->text('action_items')->nullable();
            
            // If Not Qualified
            $table->foreignId('nq_reason_id')->nullable()->constrained('nq_reasons')->nullOnDelete();
            
            // Retry scheduling
            $table->timestamp('retry_scheduled_at')->nullable();
            
            // Engagement
            $table->integer('engagement_points')->default(0);
            
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_calls');
    }
};
