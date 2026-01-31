<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmenityCategory extends Model
{
    use IsMaster;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: Amenities in this category
     */
    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class, 'category_id');
    }

    /**
     * Relationship: Active amenities only
     */
    public function activeAmenities(): HasMany
    {
        return $this->amenities()->where('is_active', true);
    }

    /**
     * Scope: With amenity counts
     */
    public function scopeWithAmenityCounts($query)
    {
        return $query->withCount(['amenities', 'activeAmenities']);
    }

    /**
     * Check if this category can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if it has amenities
        return $this->amenities()->count() === 0;
    }

    /**
     * Get total amenities count
     */
    public function getTotalAmenitiesAttribute(): int
    {
        return $this->amenities()->count();
    }

    /**
     * Get active amenities count
     */
    public function getActiveAmenitiesCountAttribute(): int
    {
        return $this->activeAmenities()->count();
    }
}
