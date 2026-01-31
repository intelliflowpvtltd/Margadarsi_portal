<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait IsMaster
{
    /**
     * Boot the trait - auto-generate slug
     */
    protected static function bootIsMaster()
    {
        static::creating(function ($model) {
            // Auto-generate slug if not provided
            if (empty($model->slug) && isset($model->name)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Scope: Get only active records
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get inactive records
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Order by sort_order then name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Search by name or slug
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }
        
        return $query->where(function($q) use ($search) {
            $q->where('name', 'ILIKE', "%{$search}%")
              ->orWhere('slug', 'ILIKE', "%{$search}%");
              
            // Also search description if available
            if (in_array('description', $this->fillable ?? [])) {
                $q->orWhere('description', 'ILIKE', "%{$search}%");
            }
        });
    }

    /**
     * Check if this record can be safely deleted
     * Override in specific models to add business logic
     */
    public function canBeDeleted(): bool
    {
        return true;
    }

    /**
     * Toggle active status
     */
    public function toggleActive(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Activate this record
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate this record
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Check if currently active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Check if currently inactive
     */
    public function isInactive(): bool
    {
        return $this->is_active === false;
    }
}
