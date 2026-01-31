<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'country_id',
        'name',
        'code',
        'slug',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class)->orderBy('sort_order')->orderBy('name');
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }

    // ==================== SCOPES ====================

    public function scopeInCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeWithCitiesCount($query)
    {
        return $query->withCount('cities');
    }

    // ==================== BUSINESS LOGIC ====================

    public function canBeDeleted(): bool
    {
        // Cannot delete if it has cities
        return $this->cities()->count() === 0;
    }

    // ==================== ACCESSORS ====================

    public function getFullNameAttribute(): string
    {
        return $this->country ? "{$this->name}, {$this->country->name}" : $this->name;
    }
}
