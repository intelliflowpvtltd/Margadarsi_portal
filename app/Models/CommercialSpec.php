<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercialSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'total_floors',
        'total_units',
        'office_units',
        'retail_units',
        'food_court_area_sqft',
        'common_area_percentage',
        'basement_levels',
        'visitor_parking_slots',
        'tenant_parking_slots',
        'green_building_certified',
        'certification_type',
    ];

    protected $casts = [
        'green_building_certified' => 'boolean',
        'food_court_area_sqft' => 'decimal:2',
        'common_area_percentage' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTotalParkingSlotsAttribute(): int
    {
        return $this->visitor_parking_slots + $this->tenant_parking_slots;
    }
}
