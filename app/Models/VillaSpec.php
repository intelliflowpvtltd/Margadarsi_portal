<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillaSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'total_villas',
        'villa_types',
        'floors_per_villa',
        'plot_sizes_sqft',
        'built_up_sizes_sqft',
        'private_garden',
        'private_pool',
        'servant_quarters',
        'car_parking_per_villa',
        'gated_community',
        'clubhouse_area_sqft',
    ];

    protected $casts = [
        'plot_sizes_sqft' => 'array',
        'built_up_sizes_sqft' => 'array',
        'private_garden' => 'boolean',
        'private_pool' => 'boolean',
        'servant_quarters' => 'boolean',
        'gated_community' => 'boolean',
        'clubhouse_area_sqft' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
