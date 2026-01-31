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
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('scheduled_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            
            // Schedule
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->string('meeting_point', 255)->nullable();
            $table->text('special_instructions')->nullable();
            $table->json('units_to_show')->nullable();
            
            // Status
            $table->string('status', 30)->default('scheduled'); // scheduled, confirmed, rescheduled, cancelled, done, no_show, partial
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rescheduled_at')->nullable();
            $table->string('reschedule_reason', 255)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason', 255)->nullable();
            
            // GPS Check-in
            $table->timestamp('checkin_at')->nullable();
            $table->decimal('checkin_latitude', 10, 8)->nullable();
            $table->decimal('checkin_longitude', 11, 8)->nullable();
            $table->boolean('checkin_gps_verified')->default(false);
            $table->string('checkin_photo_url', 500)->nullable();
            
            // GPS Check-out
            $table->timestamp('checkout_at')->nullable();
            $table->decimal('checkout_latitude', 10, 8)->nullable();
            $table->decimal('checkout_longitude', 11, 8)->nullable();
            $table->integer('visit_duration_minutes')->nullable();
            
            // Outcome
            $table->string('outcome', 30)->nullable(); // positive, handover_ready, neutral, negative, no_show, partial
            $table->text('feedback')->nullable();
            $table->string('customer_sentiment', 20)->nullable(); // very_interested, interested, neutral, not_interested
            $table->text('next_steps')->nullable();
            
            // Customer OTP Verification (optional)
            $table->string('customer_otp', 10)->nullable();
            $table->boolean('otp_verified')->default(false);
            
            // Attendees
            $table->integer('attendee_count')->default(1);
            $table->json('attendee_names')->nullable();
            
            $table->timestamps();

            $table->index(['lead_id', 'status']);
            $table->index(['project_id', 'scheduled_date']);
            $table->index(['assigned_to', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
