<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadReassignment extends Model
{
    use HasFactory;

    const REASON_WORKLOAD = 'workload';
    const REASON_PERFORMANCE = 'performance';
    const REASON_EMPLOYEE_EXIT = 'employee_exit';
    const REASON_CUSTOMER_REQUEST = 'customer_request';
    const REASON_SLA_BREACH = 'sla_breach';
    const REASON_MANUAL = 'manual';

    protected $fillable = [
        'lead_id', 'from_user_id', 'to_user_id',
        'from_team_id', 'to_team_id',
        'reason', 'notes', 'reassigned_by', 'ownership_transferred',
    ];

    protected $casts = [
        'ownership_transferred' => 'boolean',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function fromUser(): BelongsTo { return $this->belongsTo(User::class, 'from_user_id'); }
    public function toUser(): BelongsTo { return $this->belongsTo(User::class, 'to_user_id'); }
    public function fromTeam(): BelongsTo { return $this->belongsTo(Team::class, 'from_team_id'); }
    public function toTeam(): BelongsTo { return $this->belongsTo(Team::class, 'to_team_id'); }
    public function reassignedByUser(): BelongsTo { return $this->belongsTo(User::class, 'reassigned_by'); }

    public function scopeForLead($query, $leadId) { return $query->where('lead_id', $leadId); }
}
