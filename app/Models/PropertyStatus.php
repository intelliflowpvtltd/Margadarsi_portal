<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyStatus extends Model
{
    use SoftDeletes, IsMaster;

    protected $fillable = [
        'property_type_id',
        'name',
        'slug',
        'color_code',
        'badge_class',
        'workflow_order',
        'is_final_state',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'property_type_id' => 'integer',
        'workflow_order' => 'integer',
        'is_final_state' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: Property type this status belongs to
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    /**
     * Scope: Get statuses for a specific property type
     */
    public function scopeForPropertyType($query, ?int $propertyTypeId)
    {
        if ($propertyTypeId) {
            return $query->where('property_type_id', $propertyTypeId);
        }
        return $query;
    }

    /**
     * Scope: Get only pipeline states (not final)
     */
    public function scopePipeline($query)
    {
        return $query->where('is_final_state', false);
    }

    /**
     * Scope: Get only final states
     */
    public function scopeFinal($query)
    {
        return $query->where('is_final_state', true);
    }

    /**
     * Scope: Order by workflow
     */
    public function scopeWorkflowOrder($query)
    {
        return $query->orderBy('workflow_order');
    }

    /**
     * Check if this status can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Check if any projects are using this status
        return !Project::where('status_id', $this->id)->exists();
    }

    /**
     * Get badge HTML class
     */
    public function getBadgeClassAttribute($value): string
    {
        return $value ?? 'bg-secondary';
    }

    /**
     * Get color code with default
     */
    public function getColorCodeAttribute($value): string
    {
        return $value ?? '#6c757d';
    }

    /**
     * Check if this is a final state
     */
    public function isFinalState(): bool
    {
        return $this->is_final_state === true;
    }

    /**
     * Check if this is a pipeline state
     */
    public function isPipelineState(): bool
    {
        return $this->is_final_state === false;
    }
}
