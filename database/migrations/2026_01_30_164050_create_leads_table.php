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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // Customer Information
            $table->string('name', 150);
            $table->string('mobile', 15);
            $table->string('alt_mobile', 15)->nullable();
            $table->string('whatsapp', 15)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->text('address')->nullable();
            
            // Lead Classification
            $table->string('status', 30)->default('new'); // new, contacted, unreachable, qualified, not_qualified, handed_over, lost
            $table->string('sub_status', 50)->nullable();
            $table->string('stage', 30)->default('new'); // new, attempting, connected, qualified, nurturing, visit_scheduled, visit_done, handed_over
            $table->foreignId('temperature_tag_id')->nullable()->constrained('temperature_tags')->nullOnDelete();
            
            // Source & Attribution
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('source_campaign', 150)->nullable();
            $table->string('source_medium', 100)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 150)->nullable();
            
            // Assignment & Ownership
            $table->foreignId('original_owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('current_assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('assignment_rule_id')->nullable()->constrained('assignment_rules')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->boolean('ownership_locked')->default(true);
            $table->timestamp('ownership_transferred_at')->nullable();
            $table->string('ownership_transfer_reason', 50)->nullable();
            
            // Requirements
            $table->foreignId('budget_range_id')->nullable()->constrained('budget_ranges')->nullOnDelete();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->nullOnDelete();
            $table->foreignId('timeline_id')->nullable()->constrained('timelines')->nullOnDelete();
            $table->text('requirements_notes')->nullable();
            $table->boolean('budget_confirmed')->default(false);
            
            // Contact Tracking
            $table->integer('call_attempts')->default(0);
            $table->integer('connected_calls')->default(0);
            $table->timestamp('first_call_at')->nullable();
            $table->timestamp('last_call_at')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamp('next_followup_at')->nullable();
            
            // SLA Tracking
            $table->timestamp('first_call_due_at')->nullable();
            $table->boolean('sla_breached')->default(false);
            $table->integer('sla_response_seconds')->nullable();
            
            // Closure
            $table->foreignId('closure_reason_id')->nullable()->constrained('closure_reasons')->nullOnDelete();
            $table->foreignId('nq_reason_id')->nullable()->constrained('nq_reasons')->nullOnDelete();
            $table->text('closure_notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            // Engagement Score
            $table->integer('engagement_score')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            
            // Handover
            $table->timestamp('handed_over_at')->nullable();
            $table->foreignId('handed_over_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('handover_notes')->nullable();
            
            // Flags
            $table->boolean('is_duplicate')->default(false);
            $table->foreignId('duplicate_of_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->boolean('is_dormant')->default(false);
            $table->timestamp('dormant_since')->nullable();
            
            // CP Attribution (if from Channel Partner)
            $table->foreignId('cp_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cp_submitted_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'stage']);
            $table->index(['project_id', 'status']);
            $table->index(['current_assignee_id', 'status']);
            $table->index(['team_id', 'status']);
            $table->index('mobile');
            $table->index('next_followup_at');
            $table->index('first_call_due_at');
            $table->index('sla_breached');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
