<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'state_id',
        'name',
        'latitude',
        'longitude',
        'is_metro',
        'is_tier1',
        'is_tier2',
        'is_tier3',
        'pincode_prefix',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'state_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_metro' => 'boolean',
        'is_tier1' => 'boolean',
        'is_tier2' => 'boolean',
        'is_tier3' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'state_id', 'country_id');
    }

    // ==================== SCOPES ====================

    public function scopeInState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    public function scopeMetro($query)
    {
        return $query->where('is_metro', true);
    }

    public function scopeTier1($query)
    {
        return $query->where('is_tier1', true);
    }

    public function scopeTier2($query)
    {
        return $query->where('is_tier2', true);
    }

    public function scopeTier3($query)
    {
        return $query->where('is_tier3', true);
    }

    // ==================== BUSINESS LOGIC ====================

    public function canBeDeleted(): bool
    {
        // Check if any projects or leads are using this city
        return !Project::where('city_id', $this->id)->exists() 
            && !Lead::where('city_id', $this->id)->exists();
    }

    // ==================== ACCESSORS ====================

    public function getTierClassificationAttribute(): ?string
    {
        if ($this->is_tier1) return 'Tier 1';
        if ($this->is_tier2) return 'Tier 2';
        if ($this->is_tier3) return 'Tier 3';
        return null;
    }

    public function getFullNameAttribute(): string
    {
        return $this->state ? "{$this->name}, {$this->state->name}" : $this->name;
    }

    public function getFullLocationAttribute(): string
    {
        $parts = [$this->name];
        
        if ($this->state) {
            $parts[] = $this->state->name;
            if ($this->state->country) {
                $parts[] = $this->state->country->name;
            }
        }
        
        return implode(', ', $parts);
    }
}
