<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceCategory extends Model
{
    use HasFactory, IsMaster;

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
     * Get all lead sources in this category
     */
    public function leadSources(): HasMany
    {
        return $this->hasMany(LeadSource::class, 'source_category_id');
    }

    /**
     * Check if this category can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->leadSources()->count() === 0;
    }
}
