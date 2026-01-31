<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Lead Workflow Service
 * 
 * Implements the Lead Status State Machine with strict transition rules.
 * 
 * STATUS FLOW:
 * ┌─────────┐
 * │   NEW   │
 * └────┬────┘
 *      │ Call Attempt
 *      ├──────────────────┬──────────────────┐
 *      ▼                  │                  ▼
 * ┌───────────┐           │         ┌─────────────┐
 * │ CONTACTED │           │         │ UNREACHABLE │
 * └─────┬─────┘           │         └──────┬──────┘
 *       │                 │                │
 *       ├─────────────────┤     (5 attempts, no connect)
 *       ▼                 ▼                │
 * ┌───────────┐    ┌──────────────┐        │
 * │ QUALIFIED │    │NOT QUALIFIED │◄───────┘
 * └─────┬─────┘    └──────────────┘ (Final)
 *       │
 *       ├─────────────────┐
 *       ▼                 ▼
 * ┌────────────┐     ┌────────┐
 * │HANDED_OVER │     │  LOST  │
 * └────────────┘     └────────┘
 * (To Sales Dept)    (Closed)
 */
class LeadWorkflowService
{
    /**
     * Maximum call attempts before auto-marking as NOT_QUALIFIED.
     */
    const MAX_UNREACHABLE_ATTEMPTS = 5;

    /**
     * Valid status transitions map.
     * Key: current status, Value: array of allowed next statuses.
     */
    const ALLOWED_TRANSITIONS = [
        Lead::STATUS_NEW => [
            Lead::STATUS_CONTACTED,      // Connected on call
            Lead::STATUS_UNREACHABLE,    // No answer/busy/switched off
            Lead::STATUS_NOT_QUALIFIED,  // Direct NQ (wrong number, etc.)
        ],
        Lead::STATUS_CONTACTED => [
            Lead::STATUS_QUALIFIED,      // Customer interested
            Lead::STATUS_NOT_QUALIFIED,  // Not interested/budget mismatch
            Lead::STATUS_UNREACHABLE,    // Subsequent calls not answered
        ],
        Lead::STATUS_UNREACHABLE => [
            Lead::STATUS_CONTACTED,      // Finally connected
            Lead::STATUS_NOT_QUALIFIED,  // Max attempts reached or manual NQ
        ],
        Lead::STATUS_QUALIFIED => [
            Lead::STATUS_HANDED_OVER,    // Transferred to sales team
            Lead::STATUS_LOST,           // Customer backed out
            Lead::STATUS_NOT_QUALIFIED,  // Re-evaluation shows not qualified
        ],
        Lead::STATUS_NOT_QUALIFIED => [], // Final - no transitions allowed
        Lead::STATUS_HANDED_OVER => [],   // Final - no transitions allowed
        Lead::STATUS_LOST => [],          // Final - no transitions allowed
    ];

    /**
     * Stage progression based on status.
     */
    const STATUS_STAGE_MAP = [
        Lead::STATUS_NEW => Lead::STAGE_NEW,
        Lead::STATUS_CONTACTED => Lead::STAGE_CONNECTED,
        Lead::STATUS_UNREACHABLE => Lead::STAGE_ATTEMPTING,
        Lead::STATUS_QUALIFIED => Lead::STAGE_QUALIFIED,
        Lead::STATUS_NOT_QUALIFIED => null, // Keep current stage
        Lead::STATUS_HANDED_OVER => Lead::STAGE_HANDED_OVER,
        Lead::STATUS_LOST => null, // Keep current stage
    ];

    /**
     * Log a call and automatically update lead status based on outcome.
     */
    public function logCall(Lead $lead, array $callData): LeadCall
    {
        return DB::transaction(function () use ($lead, $callData) {
            // Create the call record
            $call = $lead->calls()->create([
                'user_id' => auth()->id() ?? $callData['user_id'] ?? null,
                'attempt_outcome' => $callData['attempt_outcome'],
                'call_outcome' => $callData['call_outcome'] ?? null,
                'duration_seconds' => $callData['duration_seconds'] ?? 0,
                'started_at' => $callData['started_at'] ?? now(),
                'ended_at' => $callData['ended_at'] ?? null,
                'summary' => $callData['summary'] ?? null,
                'recording_url' => $callData['recording_url'] ?? null,
                'temperature_tag_id' => $callData['temperature_tag_id'] ?? null,
                'next_followup_at' => $callData['next_followup_at'] ?? null,
                'action_items' => $callData['action_items'] ?? null,
                'nq_reason_id' => $callData['nq_reason_id'] ?? null,
                'retry_scheduled_at' => $callData['retry_scheduled_at'] ?? null,
                'engagement_points' => $this->calculateEngagementPoints($callData),
            ]);

            // Update lead call tracking
            $lead->incrementCallAttempts();

            // Process status transition based on call outcome
            $this->processCallOutcome($lead, $call);

            // Update engagement score
            $this->updateEngagementScore($lead, $call);

            return $call;
        });
    }

    /**
     * Process call outcome and transition status accordingly.
     */
    protected function processCallOutcome(Lead $lead, LeadCall $call): void
    {
        $outcome = $call->attempt_outcome;

        // Connected call
        if ($outcome === LeadCall::OUTCOME_CONNECTED) {
            $this->handleConnectedCall($lead, $call);
            return;
        }

        // Not connected - various reasons
        $this->handleNotConnectedCall($lead, $call);
    }

    /**
     * Handle connected call - transition to CONTACTED.
     */
    protected function handleConnectedCall(Lead $lead, LeadCall $call): void
    {
        // Update connected tracking
        $lead->markConnected();

        // If call resulted in qualification decision
        if ($call->call_outcome === LeadCall::CALL_QUALIFIED) {
            $this->transitionStatus($lead, Lead::STATUS_QUALIFIED, 'Customer qualified during call');
        } elseif ($call->call_outcome === LeadCall::CALL_NOT_QUALIFIED) {
            $this->transitionStatus(
                $lead, 
                Lead::STATUS_NOT_QUALIFIED, 
                'Customer not qualified during call',
                $call->nq_reason_id
            );
        }
        // Otherwise status remains CONTACTED (set by markConnected)
    }

    /**
     * Handle not connected call - may transition to UNREACHABLE.
     */
    protected function handleNotConnectedCall(Lead $lead, LeadCall $call): void
    {
        $outcome = $call->attempt_outcome;

        // Wrong number - immediate NQ
        if ($outcome === LeadCall::OUTCOME_WRONG_NUMBER) {
            $this->transitionStatus($lead, Lead::STATUS_NOT_QUALIFIED, 'Wrong number');
            return;
        }

        // Other non-connect outcomes
        if (in_array($outcome, [
            LeadCall::OUTCOME_NOT_ANSWERING,
            LeadCall::OUTCOME_BUSY,
            LeadCall::OUTCOME_SWITCHED_OFF,
            LeadCall::OUTCOME_NOT_REACHABLE,
        ])) {
            // Only transition NEW → UNREACHABLE
            if ($lead->status === Lead::STATUS_NEW) {
                $this->transitionStatus($lead, Lead::STATUS_UNREACHABLE, 'Unable to connect on first attempt');
            }

            // Check if max attempts reached
            if ($lead->call_attempts >= self::MAX_UNREACHABLE_ATTEMPTS && $lead->connected_calls === 0) {
                $this->transitionStatus(
                    $lead, 
                    Lead::STATUS_NOT_QUALIFIED, 
                    'Maximum call attempts reached without connection'
                );
            }
        }

        // Callback requested - schedule retry
        if ($outcome === LeadCall::OUTCOME_CALLBACK && $call->retry_scheduled_at) {
            $lead->update(['next_followup_at' => $call->retry_scheduled_at]);
        }
    }

    /**
     * Manually transition lead status with validation.
     */
    public function transitionStatus(
        Lead $lead, 
        string $newStatus, 
        ?string $reason = null,
        ?int $nqReasonId = null,
        ?int $closureReasonId = null
    ): bool {
        // Validate transition is allowed
        if (!$this->canTransition($lead->status, $newStatus)) {
            throw new InvalidArgumentException(
                "Invalid status transition: {$lead->status} → {$newStatus}"
            );
        }

        return DB::transaction(function () use ($lead, $newStatus, $reason, $nqReasonId, $closureReasonId) {
            $oldStatus = $lead->status;

            // Prepare update data
            $updateData = ['status' => $newStatus];

            // Handle final statuses
            if ($this->isFinalStatus($newStatus)) {
                $updateData['closed_at'] = now();
            }

            // NQ-specific fields
            if ($newStatus === Lead::STATUS_NOT_QUALIFIED && $nqReasonId) {
                $updateData['nq_reason_id'] = $nqReasonId;
                $updateData['closure_notes'] = $reason;
            }

            // Handed over specific fields
            if ($newStatus === Lead::STATUS_HANDED_OVER) {
                $updateData['handed_over_at'] = now();
                $updateData['handed_over_by'] = auth()->id();
                $updateData['handover_notes'] = $reason;
            }

            // Lost specific fields
            if ($newStatus === Lead::STATUS_LOST) {
                $updateData['closure_reason_id'] = $closureReasonId;
                $updateData['closure_notes'] = $reason;
            }

            // Update stage if applicable
            $newStage = self::STATUS_STAGE_MAP[$newStatus] ?? null;
            if ($newStage) {
                $updateData['stage'] = $newStage;
            }

            $lead->update($updateData);

            // Log activity
            $lead->logActivity(
                'status_change',
                "Status changed from {$oldStatus} to {$newStatus}" . ($reason ? ": {$reason}" : ''),
                $oldStatus,
                $newStatus,
                ['reason' => $reason, 'nq_reason_id' => $nqReasonId, 'closure_reason_id' => $closureReasonId]
            );

            return true;
        });
    }

    /**
     * Check if a status transition is allowed.
     */
    public function canTransition(string $fromStatus, string $toStatus): bool
    {
        if (!isset(self::ALLOWED_TRANSITIONS[$fromStatus])) {
            return false;
        }
        return in_array($toStatus, self::ALLOWED_TRANSITIONS[$fromStatus]);
    }

    /**
     * Get allowed next statuses for a lead.
     */
    public function getAllowedTransitions(Lead $lead): array
    {
        return self::ALLOWED_TRANSITIONS[$lead->status] ?? [];
    }

    /**
     * Check if status is final (no further transitions).
     */
    public function isFinalStatus(string $status): bool
    {
        return empty(self::ALLOWED_TRANSITIONS[$status] ?? []);
    }

    /**
     * Mark lead as qualified.
     */
    public function markQualified(Lead $lead, ?string $notes = null): bool
    {
        return $this->transitionStatus($lead, Lead::STATUS_QUALIFIED, $notes ?? 'Lead qualified');
    }

    /**
     * Mark lead as not qualified.
     */
    public function markNotQualified(Lead $lead, int $nqReasonId, ?string $notes = null): bool
    {
        return $this->transitionStatus(
            $lead, 
            Lead::STATUS_NOT_QUALIFIED, 
            $notes ?? 'Lead not qualified',
            $nqReasonId
        );
    }

    /**
     * Hand over lead to sales team.
     */
    public function handOver(Lead $lead, ?string $notes = null): bool
    {
        if ($lead->status !== Lead::STATUS_QUALIFIED) {
            throw new InvalidArgumentException('Only qualified leads can be handed over');
        }

        return $this->transitionStatus($lead, Lead::STATUS_HANDED_OVER, $notes ?? 'Handed over to sales');
    }

    /**
     * Mark lead as lost.
     */
    public function markLost(Lead $lead, int $closureReasonId, ?string $notes = null): bool
    {
        if ($lead->status !== Lead::STATUS_QUALIFIED) {
            throw new InvalidArgumentException('Only qualified leads can be marked as lost');
        }

        return $this->transitionStatus(
            $lead, 
            Lead::STATUS_LOST, 
            $notes ?? 'Lead lost',
            null,
            $closureReasonId
        );
    }

    /**
     * Schedule a follow-up call.
     */
    public function scheduleFollowup(Lead $lead, \DateTime $followupAt, ?string $notes = null): void
    {
        $lead->update(['next_followup_at' => $followupAt]);
        
        $lead->logActivity(
            'followup_scheduled',
            "Follow-up scheduled for {$followupAt->format('Y-m-d H:i')}",
            null,
            $followupAt->format('Y-m-d H:i'),
            ['notes' => $notes]
        );
    }

    /**
     * Calculate engagement points for a call.
     */
    protected function calculateEngagementPoints(array $callData): int
    {
        $points = 0;

        // Base points for connected call
        if ($callData['attempt_outcome'] === LeadCall::OUTCOME_CONNECTED) {
            $points += 10;

            // Bonus for longer calls
            $duration = $callData['duration_seconds'] ?? 0;
            if ($duration > 60) $points += 5;
            if ($duration > 180) $points += 5;
            if ($duration > 300) $points += 5;

            // Bonus for qualified outcome
            if (($callData['call_outcome'] ?? null) === LeadCall::CALL_QUALIFIED) {
                $points += 20;
            }
        } else {
            // Minimal points for attempt
            $points += 1;
        }

        return $points;
    }

    /**
     * Update lead's engagement score.
     */
    protected function updateEngagementScore(Lead $lead, LeadCall $call): void
    {
        $newScore = ($lead->engagement_score ?? 0) + ($call->engagement_points ?? 0);
        $lead->update([
            'engagement_score' => $newScore,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Get workflow statistics for a lead.
     */
    public function getWorkflowStats(Lead $lead): array
    {
        return [
            'status' => $lead->status,
            'status_label' => Lead::STATUSES[$lead->status]['label'] ?? $lead->status,
            'stage' => $lead->stage,
            'stage_label' => Lead::STAGES[$lead->stage]['label'] ?? $lead->stage,
            'is_final' => $this->isFinalStatus($lead->status),
            'allowed_transitions' => $this->getAllowedTransitions($lead),
            'call_attempts' => $lead->call_attempts,
            'connected_calls' => $lead->connected_calls,
            'remaining_attempts' => max(0, self::MAX_UNREACHABLE_ATTEMPTS - $lead->call_attempts),
            'engagement_score' => $lead->engagement_score ?? 0,
            'sla_breached' => $lead->sla_breached,
            'next_followup' => $lead->next_followup_at?->format('Y-m-d H:i'),
        ];
    }
}
