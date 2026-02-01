<?php

namespace App\Models;

use App\Traits\IsMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStatus extends Model
{
    use HasFactory, IsMaster;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'color_code',
        'badge_class',
        'workflow_order',
        'is_pipeline_state',
        'is_final_state',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_pipeline_state' => 'boolean',
        'is_final_state' => 'boolean',
        'is_active' => 'boolean',
        'workflow_order' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the company this status belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all leads with this status
     * Note: Leads table uses string 'status' column matching the slug, not a foreign key
     */
    public function leads(): HasMany
    {
        // This is a pseudo-relationship - leads use string status that matches this slug
        // For counting purposes, we use a custom query
        return $this->hasMany(Lead::class, 'status', 'slug');
    }

    /**
     * Scope: Filter by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Get pipeline statuses only
     */
    public function scopePipeline($query)
    {
        return $query->where('is_pipeline_state', true);
    }

    /**
     * Scope: Get final statuses only
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
        return $query->orderBy('workflow_order')->orderBy('name');
    }

    /**
     * Check if this lead status can be deleted
     * Uses slug to match against leads.status string column
     */
    public function canBeDeleted(): bool
    {
        return Lead::where('status', $this->slug)->count() === 0;
    }

    /**
     * Check if this is a pipeline status
     */
    public function isPipeline(): bool
    {
        return $this->is_pipeline_state === true;
    }

    /**
     * Check if this is a final status
     */
    public function isFinal(): bool
    {
        return $this->is_final_state === true;
    }
}
