<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use IsMaster;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'icon',
        'description',
        'is_premium',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: Category this amenity belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AmenityCategory::class, 'category_id');
    }

    /**
     * Relationship: Projects that have this amenity
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_amenities', 'amenity_id', 'project_id');
    }

    /**
     * Scope: Get amenities by category
     */
    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Get only premium amenities
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope: Get non-premium amenities
     */
    public function scopeStandard($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Scope: With category
     */
    public function scopeWithCategory($query)
    {
        return $query->with('category');
    }

    /**
     * Check if this amenity can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if assigned to any projects
        return $this->projects()->count() === 0;
    }

    /**
     * Check if this is a premium amenity
     */
    public function isPremium(): bool
    {
        return $this->is_premium === true;
    }

    /**
     * Check if this is a standard amenity
     */
    public function isStandard(): bool
    {
        return $this->is_premium === false;
    }

    /**
     * Get full name with category
     */
    public function getFullNameAttribute(): string
    {
        return $this->category ? "{$this->category->name} - {$this->name}" : $this->name;
    }
}
