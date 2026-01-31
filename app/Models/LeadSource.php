<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'company_id',
        'source_category_id',
        'name',
        'slug',
        'icon',
        'color_code',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the company this source belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the source category
     */
    public function sourceCategory(): BelongsTo
    {
        return $this->belongsTo(SourceCategory::class);
    }

    /**
     * Get all leads from this source
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'source_id');
    }

    /**
     * Scope: Filter by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('source_category_id', $categoryId);
    }

    /**
     * Check if this lead source can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->leads()->count() === 0;
    }
}
