<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAmenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'category',
        'icon',
        'description',
        'is_highlighted',
        'sort_order',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const CATEGORIES = [
        'lifestyle' => 'Lifestyle',
        'sports' => 'Sports & Recreation',
        'convenience' => 'Convenience',
        'security' => 'Security',
        'kids' => 'Kids',
        'health' => 'Health & Wellness',
        'green' => 'Green Spaces',
        'other' => 'Other',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function scopeHighlighted($query)
    {
        return $query->where('is_highlighted', true);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
