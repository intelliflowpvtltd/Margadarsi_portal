<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'name', 'priority',
        'project_id', 'lead_source_id', 'source_category_id', 'budget_range_id',
        'city', 'state', 'pincodes', 'time_from', 'time_to', 'cp_user_id',
        'assign_to_team_id', 'assign_to_user_id', 'is_active',
    ];

    protected $casts = [
        'pincodes' => 'array',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function leadSource(): BelongsTo { return $this->belongsTo(LeadSource::class); }
    public function sourceCategory(): BelongsTo { return $this->belongsTo(SourceCategory::class); }
    public function budgetRange(): BelongsTo { return $this->belongsTo(BudgetRange::class); }
    public function cpUser(): BelongsTo { return $this->belongsTo(User::class, 'cp_user_id'); }
    public function assignToTeam(): BelongsTo { return $this->belongsTo(Team::class, 'assign_to_team_id'); }
    public function assignToUser(): BelongsTo { return $this->belongsTo(User::class, 'assign_to_user_id'); }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeOrdered($query) { return $query->orderBy('priority', 'asc'); }

    public function matches(Lead $lead): bool
    {
        if ($this->project_id && $this->project_id !== $lead->project_id) return false;
        if ($this->lead_source_id && $this->lead_source_id !== $lead->lead_source_id) return false;
        if ($this->budget_range_id && $this->budget_range_id !== $lead->budget_range_id) return false;
        if ($this->city && strtolower($this->city) !== strtolower($lead->city ?? '')) return false;
        if ($this->state && strtolower($this->state) !== strtolower($lead->state ?? '')) return false;
        if ($this->pincodes && !in_array($lead->pincode, $this->pincodes)) return false;
        if ($this->cp_user_id && $this->cp_user_id !== $lead->cp_user_id) return false;
        if ($this->time_from && $this->time_to) {
            $now = now()->format('H:i:s');
            if ($now < $this->time_from || $now > $this->time_to) return false;
        }
        return true;
    }
}
