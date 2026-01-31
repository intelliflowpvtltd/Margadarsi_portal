<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadCall extends Model
{
    use HasFactory;

    // ==================== ATTEMPT OUTCOMES ====================
    // These indicate what happened when trying to reach the customer
    const OUTCOME_CONNECTED = 'connected';
    const OUTCOME_NOT_ANSWERING = 'not_answering';
    const OUTCOME_BUSY = 'busy';
    const OUTCOME_SWITCHED_OFF = 'switched_off';
    const OUTCOME_WRONG_NUMBER = 'wrong_number';
    const OUTCOME_CALLBACK = 'callback_requested';
    const OUTCOME_NOT_REACHABLE = 'not_reachable';
    const OUTCOME_INVALID_NUMBER = 'invalid_number';
    const OUTCOME_DND = 'dnd_activated';

    const ATTEMPT_OUTCOMES = [
        self::OUTCOME_CONNECTED => ['label' => 'Connected', 'is_connected' => true],
        self::OUTCOME_NOT_ANSWERING => ['label' => 'Not Answering', 'is_connected' => false],
        self::OUTCOME_BUSY => ['label' => 'Busy', 'is_connected' => false],
        self::OUTCOME_SWITCHED_OFF => ['label' => 'Switched Off', 'is_connected' => false],
        self::OUTCOME_WRONG_NUMBER => ['label' => 'Wrong Number', 'is_connected' => false, 'is_terminal' => true],
        self::OUTCOME_CALLBACK => ['label' => 'Callback Requested', 'is_connected' => false],
        self::OUTCOME_NOT_REACHABLE => ['label' => 'Not Reachable', 'is_connected' => false],
        self::OUTCOME_INVALID_NUMBER => ['label' => 'Invalid Number', 'is_connected' => false, 'is_terminal' => true],
        self::OUTCOME_DND => ['label' => 'DND Activated', 'is_connected' => false],
    ];

    // ==================== CALL OUTCOMES (when connected) ====================
    // These indicate the result of the conversation
    const CALL_INTERESTED = 'interested';
    const CALL_NOT_INTERESTED = 'not_interested';
    const CALL_QUALIFIED = 'qualified';
    const CALL_NOT_QUALIFIED = 'not_qualified';
    const CALL_FOLLOWUP_NEEDED = 'followup_needed';
    const CALL_THINKING = 'thinking';
    const CALL_VISIT_SCHEDULED = 'visit_scheduled';

    const CALL_OUTCOMES = [
        self::CALL_INTERESTED => ['label' => 'Interested', 'is_positive' => true],
        self::CALL_NOT_INTERESTED => ['label' => 'Not Interested', 'is_positive' => false],
        self::CALL_QUALIFIED => ['label' => 'Qualified', 'is_positive' => true, 'triggers_status' => Lead::STATUS_QUALIFIED],
        self::CALL_NOT_QUALIFIED => ['label' => 'Not Qualified', 'is_positive' => false, 'triggers_status' => Lead::STATUS_NOT_QUALIFIED],
        self::CALL_FOLLOWUP_NEEDED => ['label' => 'Follow-up Needed', 'is_positive' => true],
        self::CALL_THINKING => ['label' => 'Thinking/Considering', 'is_positive' => true],
        self::CALL_VISIT_SCHEDULED => ['label' => 'Site Visit Scheduled', 'is_positive' => true],
    ];

    protected $fillable = [
        'lead_id', 'user_id', 'attempt_outcome', 'call_outcome',
        'duration_seconds', 'started_at', 'ended_at',
        'summary', 'recording_url', 'temperature_tag_id', 'next_followup_at', 'action_items',
        'nq_reason_id', 'retry_scheduled_at', 'engagement_points',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'next_followup_at' => 'datetime',
        'retry_scheduled_at' => 'datetime',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function temperatureTag(): BelongsTo { return $this->belongsTo(TemperatureTag::class); }
    public function nqReason(): BelongsTo { return $this->belongsTo(NqReason::class); }

    public function isConnected(): bool { return $this->attempt_outcome === self::OUTCOME_CONNECTED; }

    public function scopeConnected($query) { return $query->where('attempt_outcome', self::OUTCOME_CONNECTED); }
    public function scopeForLead($query, $leadId) { return $query->where('lead_id', $leadId); }
}
