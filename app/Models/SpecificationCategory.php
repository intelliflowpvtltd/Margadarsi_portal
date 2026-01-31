<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpecificationCategory extends Model
{
    use IsMaster;

    protected $fillable = [
        'property_type_id',
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'property_type_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: Property type this category belongs to
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    /**
     * Relationship: Specification types in this category
     */
    public function specificationTypes(): HasMany
    {
        return $this->hasMany(SpecificationType::class, 'category_id');
    }

    /**
     * Relationship: Active specification types only
     */
    public function activeSpecificationTypes(): HasMany
    {
        return $this->specificationTypes()->where('is_active', true);
    }

    /**
     * Scope: Get categories for a specific property type
     */
    public function scopeForPropertyType($query, ?int $propertyTypeId)
    {
        if ($propertyTypeId) {
            return $query->where('property_type_id', $propertyTypeId);
        }
        return $query->whereNull('property_type_id');
    }

    /**
     * Scope: Global categories (not tied to property type)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('property_type_id');
    }

    /**
     * Scope: With specification type counts
     */
    public function scopeWithSpecificationCounts($query)
    {
        return $query->withCount(['specificationTypes', 'activeSpecificationTypes']);
    }

    /**
     * Check if this category can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if it has specification types
        return $this->specificationTypes()->count() === 0;
    }

    /**
     * Check if this is a global category
     */
    public function isGlobal(): bool
    {
        return $this->property_type_id === null;
    }

    /**
     * Get total specification types count
     */
    public function getTotalSpecificationTypesAttribute(): int
    {
        return $this->specificationTypes()->count();
    }
}
