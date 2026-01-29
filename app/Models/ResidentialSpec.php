<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidentialSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'total_towers',
        'total_floors_per_tower',
        'total_units',
        'units_per_floor',
        'basement_levels',
        'stilt_parking',
        'open_parking_slots',
        'covered_parking_slots',
        'clubhouse_floors',
        'clubhouse_area_sqft',
        'podium_level',
        'podium_area_sqft',
    ];

    protected $casts = [
        'stilt_parking' => 'boolean',
        'podium_level' => 'boolean',
        'clubhouse_area_sqft' => 'decimal:2',
        'podium_area_sqft' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTotalParkingSlotsAttribute(): int
    {
        return $this->open_parking_slots + $this->covered_parking_slots;
    }
}
