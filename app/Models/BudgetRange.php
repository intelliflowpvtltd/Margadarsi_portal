<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetRange extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'name',
        'slug',
        'min_amount',
        'max_amount',
        'currency',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all leads in this budget range
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'budget_range_id');
    }

    /**
     * Check if this budget range can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->leads()->count() === 0;
    }

    /**
     * Get formatted budget range display
     */
    public function getFormattedRangeAttribute(): string
    {
        if ($this->min_amount && $this->max_amount) {
            return number_format($this->min_amount) . ' - ' . number_format($this->max_amount);
        } elseif ($this->min_amount) {
            return 'Above ' . number_format($this->min_amount);
        } elseif ($this->max_amount) {
            return 'Below ' . number_format($this->max_amount);
        }
        return $this->name;
    }
}
