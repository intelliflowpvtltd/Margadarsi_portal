<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id', 'user_id', 'activity_type', 'description',
        'old_value', 'new_value', 'metadata', 'engagement_points',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopeForLead($query, $leadId) { return $query->where('lead_id', $leadId); }
    public function scopeOfType($query, $type) { return $query->where('activity_type', $type); }
}
