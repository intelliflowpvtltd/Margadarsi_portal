<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GenericMaster extends Model
{
    use IsMaster;

    protected $table = 'masters';

    protected $fillable = [
        'type',
        'name',
        'slug',
        'value',
        'metadata',
        'parent_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'metadata' => 'array',
        'parent_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Master type constants
     */
    const TYPE_PROJECT_FEATURE = 'project_feature';
    const TYPE_DOCUMENT_TYPE = 'document_type';
    const TYPE_PAYMENT_METHOD = 'payment_method';
    const TYPE_CURRENCY = 'currency';
    const TYPE_LANGUAGE = 'language';
    const TYPE_UNIT_OF_MEASURE = 'unit_of_measure';
    const TYPE_FACING_DIRECTION = 'facing_direction';
    const TYPE_FLOORING_TYPE = 'flooring_type';
    const TYPE_PARKING_TYPE = 'parking_type';

    /**
     * Relationship: Parent master (for hierarchical data)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(GenericMaster::class, 'parent_id');
    }

    /**
     * Relationship: Child masters
     */
    public function children(): HasMany
    {
        return $this->hasMany(GenericMaster::class, 'parent_id');
    }

    /**
     * Scope: Get masters by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Get root level masters (no parent)
     */
    public function scopeRootLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get child masters
     */
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope: With children
     */
    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }

    /**
     * Check if this master can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if it has children
        return $this->children()->count() === 0;
    }

    /**
     * Check if this is a root level master
     */
    public function isRootLevel(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Check if this has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get metadata value
     */
    public function getMetadataValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Set metadata value
     */
    public function setMetadataValue(string $key, $value): bool
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        return $this->update(['metadata' => $metadata]);
    }

    /**
     * Get display name with parent context
     */
    public function getFullDisplayNameAttribute(): string
    {
        if ($this->parent) {
            return "{$this->parent->name} > {$this->name}";
        }
        return $this->name;
    }

    /**
     * Static: Get all types
     */
    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_PROJECT_FEATURE => 'Project Features',
            self::TYPE_DOCUMENT_TYPE => 'Document Types',
            self::TYPE_PAYMENT_METHOD => 'Payment Methods',
            self::TYPE_CURRENCY => 'Currencies',
            self::TYPE_LANGUAGE => 'Languages',
            self::TYPE_UNIT_OF_MEASURE => 'Units of Measure',
            self::TYPE_FACING_DIRECTION => 'Facing Directions',
            self::TYPE_FLOORING_TYPE => 'Flooring Types',
            self::TYPE_PARKING_TYPE => 'Parking Types',
        ];
    }
}
