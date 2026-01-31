<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    // ==================== STATUS CONSTANTS ====================
    const STATUS_NEW = 'new';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_UNREACHABLE = 'unreachable';
    const STATUS_QUALIFIED = 'qualified';
    const STATUS_NOT_QUALIFIED = 'not_qualified';
    const STATUS_HANDED_OVER = 'handed_over';
    const STATUS_LOST = 'lost';

    const STATUSES = [
        self::STATUS_NEW => ['label' => 'New', 'color' => '#3B82F6', 'is_final' => false],
        self::STATUS_CONTACTED => ['label' => 'Contacted', 'color' => '#8B5CF6', 'is_final' => false],
        self::STATUS_UNREACHABLE => ['label' => 'Unreachable', 'color' => '#F59E0B', 'is_final' => false],
        self::STATUS_QUALIFIED => ['label' => 'Qualified', 'color' => '#10B981', 'is_final' => false],
        self::STATUS_NOT_QUALIFIED => ['label' => 'Not Qualified', 'color' => '#6B7280', 'is_final' => true],
        self::STATUS_HANDED_OVER => ['label' => 'Handed Over', 'color' => '#22C55E', 'is_final' => true],
        self::STATUS_LOST => ['label' => 'Lost', 'color' => '#EF4444', 'is_final' => true],
    ];

    // ==================== STAGE CONSTANTS ====================
    const STAGE_NEW = 'new';
    const STAGE_ATTEMPTING = 'attempting';
    const STAGE_CONNECTED = 'connected';
    const STAGE_QUALIFIED = 'qualified';
    const STAGE_NURTURING = 'nurturing';
    const STAGE_VISIT_SCHEDULED = 'visit_scheduled';
    const STAGE_VISIT_DONE = 'visit_done';
    const STAGE_HANDED_OVER = 'handed_over';

    const STAGES = [
        self::STAGE_NEW => ['label' => 'New', 'order' => 1, 'phase' => 'acquisition'],
        self::STAGE_ATTEMPTING => ['label' => 'Attempting', 'order' => 2, 'phase' => 'acquisition'],
        self::STAGE_CONNECTED => ['label' => 'Connected', 'order' => 3, 'phase' => 'acquisition'],
        self::STAGE_QUALIFIED => ['label' => 'Qualified', 'order' => 4, 'phase' => 'engagement'],
        self::STAGE_NURTURING => ['label' => 'Nurturing', 'order' => 5, 'phase' => 'engagement'],
        self::STAGE_VISIT_SCHEDULED => ['label' => 'Visit Scheduled', 'order' => 6, 'phase' => 'engagement'],
        self::STAGE_VISIT_DONE => ['label' => 'Visit Done', 'order' => 7, 'phase' => 'handover'],
        self::STAGE_HANDED_OVER => ['label' => 'Handed Over', 'order' => 8, 'phase' => 'handover'],
    ];

    // ==================== SLA CONSTANTS (minutes) ====================
    const SLA_HOT = 5;
    const SLA_WARM = 15;
    const SLA_COLD = 30;

    // ==================== SUB-STATUS CONSTANTS ====================
    const SUB_STATUS_INTERESTED = 'interested';
    const SUB_STATUS_THINKING = 'thinking';
    const SUB_STATUS_CALLBACK = 'callback_requested';
    const SUB_STATUS_BUSY = 'busy';
    const SUB_STATUS_NOT_ANSWERING = 'not_answering';
    const SUB_STATUS_SWITCHED_OFF = 'switched_off';
    const SUB_STATUS_BUDGET_MISMATCH = 'budget_mismatch';
    const SUB_STATUS_LOCATION_ISSUE = 'location_issue';
    const SUB_STATUS_ALREADY_PURCHASED = 'already_purchased';
    const SUB_STATUS_WRONG_NUMBER = 'wrong_number';

    // ==================== TRANSITION RULES ====================
    const ALLOWED_TRANSITIONS = [
        self::STATUS_NEW => [
            self::STATUS_CONTACTED,
            self::STATUS_UNREACHABLE,
            self::STATUS_NOT_QUALIFIED,
        ],
        self::STATUS_CONTACTED => [
            self::STATUS_QUALIFIED,
            self::STATUS_NOT_QUALIFIED,
            self::STATUS_UNREACHABLE,
        ],
        self::STATUS_UNREACHABLE => [
            self::STATUS_CONTACTED,
            self::STATUS_NOT_QUALIFIED,
        ],
        self::STATUS_QUALIFIED => [
            self::STATUS_HANDED_OVER,
            self::STATUS_LOST,
            self::STATUS_NOT_QUALIFIED,
        ],
        self::STATUS_NOT_QUALIFIED => [],
        self::STATUS_HANDED_OVER => [],
        self::STATUS_LOST => [],
    ];

    const MAX_UNREACHABLE_ATTEMPTS = 5;

    protected $fillable = [
        'company_id', 'project_id',
        'name', 'mobile', 'alt_mobile', 'whatsapp', 'email',
        'city', 'state', 'pincode', 'address',
        'status', 'sub_status', 'stage', 'temperature_tag_id',
        'lead_source_id', 'source_campaign', 'source_medium',
        'utm_source', 'utm_medium', 'utm_campaign',
        'original_owner_id', 'current_assignee_id', 'team_id', 'assignment_rule_id',
        'assigned_at', 'ownership_locked', 'ownership_transferred_at', 'ownership_transfer_reason',
        'budget_range_id', 'property_type_id', 'timeline_id', 'requirements_notes', 'budget_confirmed',
        'call_attempts', 'connected_calls', 'first_call_at', 'last_call_at', 'last_connected_at', 'next_followup_at',
        'first_call_due_at', 'sla_breached', 'sla_response_seconds',
        'closure_reason_id', 'nq_reason_id', 'closure_notes', 'closed_at',
        'engagement_score', 'last_activity_at',
        'handed_over_at', 'handed_over_by', 'handover_notes',
        'is_duplicate', 'duplicate_of_id', 'is_dormant', 'dormant_since',
        'cp_user_id', 'cp_submitted_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'ownership_transferred_at' => 'datetime',
        'first_call_at' => 'datetime',
        'last_call_at' => 'datetime',
        'last_connected_at' => 'datetime',
        'next_followup_at' => 'datetime',
        'first_call_due_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'handed_over_at' => 'datetime',
        'dormant_since' => 'datetime',
        'cp_submitted_at' => 'datetime',
        'ownership_locked' => 'boolean',
        'sla_breached' => 'boolean',
        'budget_confirmed' => 'boolean',
        'is_duplicate' => 'boolean',
        'is_dormant' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function temperatureTag(): BelongsTo
    {
        return $this->belongsTo(TemperatureTag::class);
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function originalOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_owner_id');
    }

    public function currentAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_assignee_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assignmentRule(): BelongsTo
    {
        return $this->belongsTo(AssignmentRule::class);
    }

    public function budgetRange(): BelongsTo
    {
        return $this->belongsTo(BudgetRange::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function timeline(): BelongsTo
    {
        return $this->belongsTo(Timeline::class);
    }

    public function closureReason(): BelongsTo
    {
        return $this->belongsTo(ClosureReason::class);
    }

    public function nqReason(): BelongsTo
    {
        return $this->belongsTo(NqReason::class);
    }

    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'duplicate_of_id');
    }

    public function cpUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cp_user_id');
    }

    public function calls(): HasMany
    {
        return $this->hasMany(LeadCall::class)->orderBy('created_at', 'desc');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->orderBy('created_at', 'desc');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class)->orderBy('scheduled_date', 'desc');
    }

    public function reassignments(): HasMany
    {
        return $this->hasMany(LeadReassignment::class)->orderBy('created_at', 'desc');
    }

    // ==================== SCOPES ====================

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('current_assignee_id', $userId);
    }

    public function scopeForTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWithStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_NOT_QUALIFIED, self::STATUS_HANDED_OVER, self::STATUS_LOST]);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', [self::STATUS_NOT_QUALIFIED, self::STATUS_HANDED_OVER, self::STATUS_LOST]);
    }

    public function scopeSlaBreach($query)
    {
        return $query->where('sla_breached', true);
    }

    public function scopeDormant($query)
    {
        return $query->where('is_dormant', true);
    }

    public function scopeFollowupDue($query)
    {
        return $query->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', now())
            ->active();
    }

    /**
     * CRITICAL SCOPE: Apply role-based visibility filtering.
     * This ensures users only see leads they have access to based on their role.
     * 
     * Usage: Lead::visibleTo($user)->get()
     */
    public function scopeVisibleTo($query, User $user)
    {
        $scopeService = app(\App\Services\LeadScopeService::class);
        return $scopeService->applyScope($query, $user);
    }

    /**
     * Scope for leads visible to the currently authenticated user.
     * 
     * Usage: Lead::visibleToCurrentUser()->get()
     */
    public function scopeVisibleToCurrentUser($query)
    {
        $user = auth()->user();
        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return empty if no auth
        }
        return $query->visibleTo($user);
    }

    // ==================== METHODS ====================

    public function isFinalStatus(): bool
    {
        return self::STATUSES[$this->status]['is_final'] ?? false;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        if (!isset(self::ALLOWED_TRANSITIONS[$this->status])) {
            return false;
        }
        return in_array($newStatus, self::ALLOWED_TRANSITIONS[$this->status]);
    }

    /**
     * Get all allowed next statuses from current status.
     */
    public function getAllowedTransitions(): array
    {
        return self::ALLOWED_TRANSITIONS[$this->status] ?? [];
    }

    /**
     * Check if lead has reached max unreachable attempts.
     */
    public function hasReachedMaxAttempts(): bool
    {
        return $this->call_attempts >= self::MAX_UNREACHABLE_ATTEMPTS && $this->connected_calls === 0;
    }

    public function updateStatus(string $newStatus, ?string $subStatus = null): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->sub_status = $subStatus;

        if (self::STATUSES[$newStatus]['is_final']) {
            $this->closed_at = now();
        }

        $this->save();

        $this->logActivity('status_change', "Status changed from {$oldStatus} to {$newStatus}", $oldStatus, $newStatus);

        return true;
    }

    public function updateStage(string $newStage): bool
    {
        if (!array_key_exists($newStage, self::STAGES)) {
            return false;
        }

        $oldStage = $this->stage;
        $this->stage = $newStage;
        $this->save();

        $this->logActivity('stage_change', "Stage changed from {$oldStage} to {$newStage}", $oldStage, $newStage);

        return true;
    }

    public function logActivity(string $type, string $description, ?string $oldValue = null, ?string $newValue = null, array $metadata = []): LeadActivity
    {
        return $this->activities()->create([
            'user_id' => auth()->id(),
            'activity_type' => $type,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'metadata' => $metadata,
        ]);
    }

    public function incrementCallAttempts(): void
    {
        $this->increment('call_attempts');
        if (!$this->first_call_at) {
            $this->update(['first_call_at' => now()]);
        }
        $this->update(['last_call_at' => now()]);
    }

    public function markConnected(): void
    {
        $this->increment('connected_calls');
        $this->update(['last_connected_at' => now()]);

        if ($this->stage === self::STAGE_NEW || $this->stage === self::STAGE_ATTEMPTING) {
            $this->updateStage(self::STAGE_CONNECTED);
        }

        if ($this->status === self::STATUS_NEW) {
            $this->updateStatus(self::STATUS_CONTACTED);
        }
    }

    public function checkSlaCompliance(): void
    {
        if ($this->first_call_due_at && !$this->sla_breached && !$this->first_call_at) {
            if (now()->gt($this->first_call_due_at)) {
                $this->update(['sla_breached' => true]);
            }
        }

        if ($this->first_call_at && $this->first_call_due_at && !$this->sla_response_seconds) {
            $responseSeconds = $this->first_call_at->diffInSeconds($this->created_at);
            $this->update(['sla_response_seconds' => $responseSeconds]);
        }
    }

    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) > 10 && substr($phone, 0, 2) === '91') {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) > 10 && substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }
        return substr($phone, -10);
    }
}
