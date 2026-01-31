<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'icon',
        'phone_code',
        'currency_code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function states(): HasMany
    {
        return $this->hasMany(State::class)->orderBy('sort_order')->orderBy('name');
    }

    public function cities(): HasManyThrough
    {
        return $this->hasManyThrough(City::class, State::class);
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }

    // ==================== SCOPES ====================

    public function scopeWithStates($query)
    {
        return $query->with('states');
    }

    public function scopeWithCitiesCount($query)
    {
        return $query->withCount('cities');
    }

    // ==================== BUSINESS LOGIC ====================

    public function canBeDeleted(): bool
    {
        // Cannot delete if it has states
        return $this->states()->count() === 0;
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameWithCodeAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }
}
