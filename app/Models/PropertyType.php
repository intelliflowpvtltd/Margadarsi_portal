<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyType extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color_code',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all property statuses for this type
     */
    public function propertyStatuses(): HasMany
    {
        return $this->hasMany(PropertyStatus::class, 'property_type_id');
    }

    /**
     * Get all projects of this type
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'type_id');
    }

    /**
     * Check if this property type can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if it has projects or property statuses
        return $this->projects()->count() === 0 
            && $this->propertyStatuses()->count() === 0;
    }
}
