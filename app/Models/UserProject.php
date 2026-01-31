<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserProject extends Pivot
{
    protected $table = 'user_projects';

    protected $fillable = [
        'user_id',
        'project_id',
        'access_level',
        'is_available_for_leads',
        'max_active_leads',
        'current_active_leads',
        'assignment_weight',
        'last_lead_assigned_at',
        'assigned_at',
        'assigned_by',
    ];

    protected $casts = [
        'is_available_for_leads' => 'boolean',
        'last_lead_assigned_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if user can receive more leads.
     */
    public function canReceiveLeads(): bool
    {
        return $this->is_available_for_leads && 
               $this->current_active_leads < $this->max_active_leads;
    }

    /**
     * Increment active leads count and update last assigned timestamp.
     */
    public function incrementActiveLeads(): void
    {
        $this->increment('current_active_leads');
        $this->update(['last_lead_assigned_at' => now()]);
    }

    /**
     * Decrement active leads count.
     */
    public function decrementActiveLeads(): void
    {
        if ($this->current_active_leads > 0) {
            $this->decrement('current_active_leads');
        }
    }

    /**
     * Scope for available users who can receive leads.
     */
    public function scopeAvailableForLeads($query)
    {
        return $query->where('is_available_for_leads', true)
            ->whereRaw('current_active_leads < max_active_leads');
    }

    /**
     * Scope to order by round-robin priority.
     * Users with fewer leads relative to their weight get priority,
     * then by last_lead_assigned_at (oldest first for fair rotation).
     */
    public function scopeRoundRobinOrder($query)
    {
        return $query->orderByRaw('current_active_leads / NULLIF(assignment_weight, 0) ASC')
            ->orderByRaw('last_lead_assigned_at ASC NULLS FIRST');
    }
}
