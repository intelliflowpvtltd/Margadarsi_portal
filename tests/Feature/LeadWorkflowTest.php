<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\NqReason;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Services\LeadWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected Project $project;
    protected User $user;
    protected LeadWorkflowService $workflowService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        
        Role::createSystemRolesForCompany($this->company->id);
        $role = Role::where('company_id', $this->company->id)->where('slug', 'telecaller')->first();

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $role->id,
        ]);

        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->workflowService = app(LeadWorkflowService::class);
        $this->actingAs($this->user);
    }

    protected function createLead(array $attributes = []): Lead
    {
        return Lead::factory()->create(array_merge([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'current_assignee_id' => $this->user->id,
            'status' => Lead::STATUS_NEW,
            'stage' => Lead::STAGE_NEW,
        ], $attributes));
    }

    // ==================== STATUS TRANSITION TESTS ====================

    /** @test */
    public function new_lead_can_transition_to_contacted()
    {
        $lead = $this->createLead();

        $this->assertTrue($lead->canTransitionTo(Lead::STATUS_CONTACTED));
        
        $result = $this->workflowService->transitionStatus($lead, Lead::STATUS_CONTACTED, 'Connected on first call');
        
        $this->assertTrue($result);
        $this->assertEquals(Lead::STATUS_CONTACTED, $lead->fresh()->status);
    }

    /** @test */
    public function new_lead_can_transition_to_unreachable()
    {
        $lead = $this->createLead();

        $this->assertTrue($lead->canTransitionTo(Lead::STATUS_UNREACHABLE));
        
        $result = $this->workflowService->transitionStatus($lead, Lead::STATUS_UNREACHABLE, 'No answer');
        
        $this->assertTrue($result);
        $this->assertEquals(Lead::STATUS_UNREACHABLE, $lead->fresh()->status);
    }

    /** @test */
    public function new_lead_cannot_transition_directly_to_qualified()
    {
        $lead = $this->createLead();

        $this->assertFalse($lead->canTransitionTo(Lead::STATUS_QUALIFIED));
        
        $this->expectException(\InvalidArgumentException::class);
        $this->workflowService->transitionStatus($lead, Lead::STATUS_QUALIFIED);
    }

    /** @test */
    public function contacted_lead_can_be_qualified()
    {
        $lead = $this->createLead(['status' => Lead::STATUS_CONTACTED]);

        $this->assertTrue($lead->canTransitionTo(Lead::STATUS_QUALIFIED));
        
        $result = $this->workflowService->markQualified($lead, 'Customer interested in 3BHK');
        
        $this->assertTrue($result);
        $this->assertEquals(Lead::STATUS_QUALIFIED, $lead->fresh()->status);
    }

    /** @test */
    public function contacted_lead_can_be_not_qualified()
    {
        $lead = $this->createLead(['status' => Lead::STATUS_CONTACTED]);
        $nqReason = NqReason::factory()->create(['company_id' => $this->company->id]);

        $result = $this->workflowService->markNotQualified($lead, $nqReason->id, 'Budget too low');
        
        $this->assertTrue($result);
        $lead->refresh();
        $this->assertEquals(Lead::STATUS_NOT_QUALIFIED, $lead->status);
        $this->assertEquals($nqReason->id, $lead->nq_reason_id);
        $this->assertNotNull($lead->closed_at);
    }

    /** @test */
    public function qualified_lead_can_be_handed_over()
    {
        $lead = $this->createLead(['status' => Lead::STATUS_QUALIFIED]);

        $result = $this->workflowService->handOver($lead, 'Ready for sales team');
        
        $this->assertTrue($result);
        $lead->refresh();
        $this->assertEquals(Lead::STATUS_HANDED_OVER, $lead->status);
        $this->assertNotNull($lead->handed_over_at);
        $this->assertEquals($this->user->id, $lead->handed_over_by);
    }

    /** @test */
    public function only_qualified_leads_can_be_handed_over()
    {
        $lead = $this->createLead(['status' => Lead::STATUS_CONTACTED]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only qualified leads can be handed over');
        
        $this->workflowService->handOver($lead);
    }

    /** @test */
    public function final_status_cannot_transition()
    {
        $lead = $this->createLead(['status' => Lead::STATUS_NOT_QUALIFIED]);

        $this->assertTrue($lead->isFinalStatus());
        $this->assertEmpty($lead->getAllowedTransitions());
        $this->assertFalse($lead->canTransitionTo(Lead::STATUS_CONTACTED));
    }

    // ==================== CALL LOGGING TESTS ====================

    /** @test */
    public function logging_connected_call_transitions_new_to_contacted()
    {
        $lead = $this->createLead();

        $call = $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_CONNECTED,
            'call_outcome' => LeadCall::CALL_INTERESTED,
            'duration_seconds' => 120,
            'summary' => 'Customer interested in project',
        ]);

        $this->assertInstanceOf(LeadCall::class, $call);
        $this->assertEquals(Lead::STATUS_CONTACTED, $lead->fresh()->status);
        $this->assertEquals(1, $lead->fresh()->call_attempts);
        $this->assertEquals(1, $lead->fresh()->connected_calls);
    }

    /** @test */
    public function logging_not_answering_call_transitions_new_to_unreachable()
    {
        $lead = $this->createLead();

        $call = $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_NOT_ANSWERING,
        ]);

        $this->assertEquals(Lead::STATUS_UNREACHABLE, $lead->fresh()->status);
        $this->assertEquals(1, $lead->fresh()->call_attempts);
        $this->assertEquals(0, $lead->fresh()->connected_calls);
    }

    /** @test */
    public function wrong_number_immediately_marks_not_qualified()
    {
        $lead = $this->createLead();

        $call = $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_WRONG_NUMBER,
        ]);

        $this->assertEquals(Lead::STATUS_NOT_QUALIFIED, $lead->fresh()->status);
    }

    /** @test */
    public function max_unreachable_attempts_marks_not_qualified()
    {
        $lead = $this->createLead([
            'status' => Lead::STATUS_UNREACHABLE,
            'call_attempts' => Lead::MAX_UNREACHABLE_ATTEMPTS - 1,
            'connected_calls' => 0,
        ]);

        $call = $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_NOT_ANSWERING,
        ]);

        $this->assertEquals(Lead::STATUS_NOT_QUALIFIED, $lead->fresh()->status);
    }

    /** @test */
    public function connected_call_with_qualified_outcome_transitions_to_qualified()
    {
        $lead = $this->createLead();

        $call = $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_CONNECTED,
            'call_outcome' => LeadCall::CALL_QUALIFIED,
            'duration_seconds' => 300,
            'summary' => 'Customer ready to visit site',
        ]);

        $this->assertEquals(Lead::STATUS_QUALIFIED, $lead->fresh()->status);
    }

    /** @test */
    public function connected_call_with_not_qualified_outcome_transitions_to_nq()
    {
        $lead = $this->createLead();
        $nqReason = NqReason::factory()->create(['company_id' => $this->company->id]);

        $call = $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_CONNECTED,
            'call_outcome' => LeadCall::CALL_NOT_QUALIFIED,
            'nq_reason_id' => $nqReason->id,
            'summary' => 'Budget mismatch',
        ]);

        $this->assertEquals(Lead::STATUS_NOT_QUALIFIED, $lead->fresh()->status);
    }

    // ==================== ENGAGEMENT SCORE TESTS ====================

    /** @test */
    public function connected_call_increases_engagement_score()
    {
        $lead = $this->createLead(['engagement_score' => 0]);

        $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_CONNECTED,
            'duration_seconds' => 200,
        ]);

        $this->assertGreaterThan(0, $lead->fresh()->engagement_score);
    }

    /** @test */
    public function longer_calls_get_more_engagement_points()
    {
        $lead1 = $this->createLead(['engagement_score' => 0]);
        $lead2 = $this->createLead(['engagement_score' => 0]);

        $this->workflowService->logCall($lead1, [
            'attempt_outcome' => LeadCall::OUTCOME_CONNECTED,
            'duration_seconds' => 30, // Short call
        ]);

        $this->workflowService->logCall($lead2, [
            'attempt_outcome' => LeadCall::OUTCOME_CONNECTED,
            'duration_seconds' => 400, // Long call
        ]);

        $this->assertGreaterThan($lead1->fresh()->engagement_score, $lead2->fresh()->engagement_score);
    }

    // ==================== WORKFLOW STATS TESTS ====================

    /** @test */
    public function get_workflow_stats_returns_complete_info()
    {
        $lead = $this->createLead([
            'call_attempts' => 2,
            'connected_calls' => 1,
            'engagement_score' => 25,
        ]);

        $stats = $this->workflowService->getWorkflowStats($lead);

        $this->assertArrayHasKey('status', $stats);
        $this->assertArrayHasKey('status_label', $stats);
        $this->assertArrayHasKey('stage', $stats);
        $this->assertArrayHasKey('is_final', $stats);
        $this->assertArrayHasKey('allowed_transitions', $stats);
        $this->assertArrayHasKey('call_attempts', $stats);
        $this->assertArrayHasKey('connected_calls', $stats);
        $this->assertArrayHasKey('remaining_attempts', $stats);
        $this->assertArrayHasKey('engagement_score', $stats);
        
        $this->assertEquals('New', $stats['status_label']);
        $this->assertEquals(2, $stats['call_attempts']);
        $this->assertEquals(25, $stats['engagement_score']);
    }

    // ==================== FOLLOWUP SCHEDULING TESTS ====================

    /** @test */
    public function can_schedule_followup()
    {
        $lead = $this->createLead();
        $followupDate = now()->addDays(2);

        $this->workflowService->scheduleFollowup($lead, $followupDate, 'Call back after site visit');

        $this->assertEquals(
            $followupDate->format('Y-m-d H:i'),
            $lead->fresh()->next_followup_at->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function callback_requested_schedules_retry()
    {
        $lead = $this->createLead();
        $retryDate = now()->addHours(4);

        $this->workflowService->logCall($lead, [
            'attempt_outcome' => LeadCall::OUTCOME_CALLBACK,
            'retry_scheduled_at' => $retryDate,
        ]);

        $this->assertEquals(
            $retryDate->format('Y-m-d H:i'),
            $lead->fresh()->next_followup_at->format('Y-m-d H:i')
        );
    }

    // ==================== ACTIVITY LOGGING TESTS ====================

    /** @test */
    public function status_transitions_are_logged_as_activities()
    {
        $lead = $this->createLead();

        $this->workflowService->transitionStatus($lead, Lead::STATUS_CONTACTED, 'First contact made');

        $activity = $lead->activities()->latest()->first();
        
        $this->assertEquals('status_change', $activity->activity_type);
        $this->assertStringContainsString('new', $activity->old_value);
        $this->assertStringContainsString('contacted', $activity->new_value);
    }
}
