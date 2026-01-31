<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecificationType extends Model
{
    use IsMaster;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'data_type',
        'allowed_values',
        'unit',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'allowed_values' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Data type constants
     */
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_NUMBER = 'number';
    const DATA_TYPE_BOOLEAN = 'boolean';
    const DATA_TYPE_SELECT = 'select';
    const DATA_TYPE_DATE = 'date';
    const DATA_TYPE_TEXTAREA = 'textarea';

    /**
     * Relationship: Category this specification type belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SpecificationCategory::class, 'category_id');
    }

    /**
     * Scope: Get specifications by category
     */
    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Get required specifications
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope: Get optional specifications
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    /**
     * Scope: Get by data type
     */
    public function scopeByDataType($query, string $dataType)
    {
        return $query->where('data_type', $dataType);
    }

    /**
     * Check if this specification type can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Add check for existing project specifications if needed
        return true;
    }

    /**
     * Check if this is a required field
     */
    public function isRequired(): bool
    {
        return $this->is_required === true;
    }

    /**
     * Check if this is a select type
     */
    public function isSelectType(): bool
    {
        return $this->data_type === self::DATA_TYPE_SELECT;
    }

    /**
     * Get allowed values as array
     */
    public function getAllowedValuesArrayAttribute(): array
    {
        return is_array($this->allowed_values) ? $this->allowed_values : [];
    }

    /**
     * Get full name with category and unit
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->category ? "{$this->category->name} - {$this->name}" : $this->name;
        
        if ($this->unit) {
            $name .= " ({$this->unit})";
        }
        
        return $name;
    }

    /**
     * Validate value based on data type
     */
    public function validateValue($value): bool
    {
        switch ($this->data_type) {
            case self::DATA_TYPE_NUMBER:
                return is_numeric($value);
            case self::DATA_TYPE_BOOLEAN:
                return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false']);
            case self::DATA_TYPE_SELECT:
                return in_array($value, $this->allowed_values_array);
            case self::DATA_TYPE_DATE:
                return strtotime($value) !== false;
            default:
                return true;
        }
    }
}
