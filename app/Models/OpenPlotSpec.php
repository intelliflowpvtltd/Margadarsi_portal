<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenPlotSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'total_plots',
        'plot_sizes',
        'min_plot_size_sqyds',
        'max_plot_size_sqyds',
        'road_width_feet',
        'underground_drainage',
        'underground_electricity',
        'water_supply',
        'compound_wall',
        'avenue_plantation',
        'fencing',
        'park_area_sqft',
        'community_hall_sqft',
    ];

    protected $casts = [
        'plot_sizes' => 'array',
        'road_width_feet' => 'array',
        'underground_drainage' => 'boolean',
        'underground_electricity' => 'boolean',
        'water_supply' => 'boolean',
        'compound_wall' => 'boolean',
        'avenue_plantation' => 'boolean',
        'fencing' => 'boolean',
        'min_plot_size_sqyds' => 'decimal:2',
        'max_plot_size_sqyds' => 'decimal:2',
        'park_area_sqft' => 'decimal:2',
        'community_hall_sqft' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get infrastructure features as array.
     */
    public function getInfrastructureFeaturesAttribute(): array
    {
        $features = [];
        if ($this->underground_drainage) $features[] = 'Underground Drainage';
        if ($this->underground_electricity) $features[] = 'Underground Electricity';
        if ($this->water_supply) $features[] = 'Water Supply';
        if ($this->compound_wall) $features[] = 'Compound Wall';
        if ($this->avenue_plantation) $features[] = 'Avenue Plantation';
        if ($this->fencing) $features[] = 'Plot Fencing';
        return $features;
    }
}
