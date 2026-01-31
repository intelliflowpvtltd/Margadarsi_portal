<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'team_role',
        'assignment_weight',
        'max_active_leads',
        'current_active_leads',
        'is_available',
        'available_from',
        'available_to',
        'working_days',
        'last_assigned_at',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'working_days' => 'array',
        'last_assigned_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->whereRaw('current_active_leads < max_active_leads');
    }

    public function incrementActiveLeads(): void
    {
        $this->increment('current_active_leads');
        $this->update(['last_assigned_at' => now()]);
    }

    public function decrementActiveLeads(): void
    {
        if ($this->current_active_leads > 0) {
            $this->decrement('current_active_leads');
        }
    }

    public function isWithinWorkingHours(): bool
    {
        if (!$this->available_from || !$this->available_to) {
            return true;
        }
        $now = now()->format('H:i:s');
        return $now >= $this->available_from && $now <= $this->available_to;
    }

    public function isWorkingDay(): bool
    {
        if (!$this->working_days) {
            return true;
        }
        return in_array(now()->dayOfWeek, $this->working_days);
    }
}
