<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'image_path',
        'title',
        'alt_text',
        'type',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const TYPES = [
        'gallery' => 'Gallery',
        'floor_plan' => 'Floor Plan',
        'master_plan' => 'Master Plan',
        'brochure' => 'Brochure',
        'elevation' => 'Elevation',
        'amenity' => 'Amenity',
        'other' => 'Other',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
