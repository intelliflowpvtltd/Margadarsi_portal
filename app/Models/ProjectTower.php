<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTower extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'total_floors',
        'units_per_floor',
        'basement_levels',
        'has_terrace',
        'status',
        'completion_date',
        'sort_order',
    ];

    protected $casts = [
        'has_terrace' => 'boolean',
        'completion_date' => 'date',
        'sort_order' => 'integer',
    ];

    public const STATUSES = [
        'upcoming' => 'Upcoming',
        'construction' => 'Under Construction',
        'completed' => 'Completed',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTotalUnitsAttribute(): int
    {
        return ($this->total_floors ?? 0) * ($this->units_per_floor ?? 0);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
