<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Timeline extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'name',
        'min_days',
        'max_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'min_days' => 'integer',
        'max_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all leads with this timeline
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'timeline_id');
    }

    /**
     * Check if this timeline can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->leads()->count() === 0;
    }
}
