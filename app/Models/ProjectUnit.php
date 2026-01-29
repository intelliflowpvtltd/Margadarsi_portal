<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'type',
        'carpet_area_sqft',
        'built_up_area_sqft',
        'super_built_up_sqft',
        'plot_area_sqyds',
        'bedrooms',
        'bathrooms',
        'balconies',
        'facing',
        'floor_plan_image',
        '3d_view_image',
        'price_on_request',
        'base_price',
        'price_per_sqft',
        'total_units',
        'available_units',
        'booked_units',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'carpet_area_sqft' => 'decimal:2',
        'built_up_area_sqft' => 'decimal:2',
        'super_built_up_sqft' => 'decimal:2',
        'plot_area_sqyds' => 'decimal:2',
        'base_price' => 'decimal:2',
        'price_per_sqft' => 'decimal:2',
        'price_on_request' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        '1bhk' => '1 BHK',
        '2bhk' => '2 BHK',
        '3bhk' => '3 BHK',
        '4bhk' => '4 BHK',
        '5bhk' => '5 BHK',
        'studio' => 'Studio',
        'penthouse' => 'Penthouse',
        'duplex' => 'Duplex',
        'shop' => 'Shop',
        'office' => 'Office',
        'showroom' => 'Showroom',
        'plot' => 'Plot',
        'villa' => 'Villa',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getSoldUnitsAttribute(): int
    {
        return $this->total_units - $this->available_units;
    }

    public function getAvailabilityPercentageAttribute(): float
    {
        if ($this->total_units === 0) return 0;
        return round(($this->available_units / $this->total_units) * 100, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
