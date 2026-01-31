<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisit extends Model
{
    use HasFactory;

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_RESCHEDULED = 'rescheduled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DONE = 'done';
    const STATUS_NO_SHOW = 'no_show';
    const STATUS_PARTIAL = 'partial';

    const OUTCOME_POSITIVE = 'positive';
    const OUTCOME_HANDOVER_READY = 'handover_ready';
    const OUTCOME_NEUTRAL = 'neutral';
    const OUTCOME_NEGATIVE = 'negative';
    const OUTCOME_NO_SHOW = 'no_show';
    const OUTCOME_PARTIAL = 'partial';

    protected $fillable = [
        'lead_id', 'project_id', 'scheduled_by', 'assigned_to',
        'scheduled_date', 'scheduled_time', 'meeting_point', 'special_instructions', 'units_to_show',
        'status', 'confirmed_at', 'rescheduled_at', 'reschedule_reason', 'cancelled_at', 'cancellation_reason',
        'checkin_at', 'checkin_latitude', 'checkin_longitude', 'checkin_gps_verified', 'checkin_photo_url',
        'checkout_at', 'checkout_latitude', 'checkout_longitude', 'visit_duration_minutes',
        'outcome', 'feedback', 'customer_sentiment', 'next_steps',
        'customer_otp', 'otp_verified', 'attendee_count', 'attendee_names',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'confirmed_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
        'checkin_gps_verified' => 'boolean',
        'otp_verified' => 'boolean',
        'units_to_show' => 'array',
        'attendee_names' => 'array',
        'checkin_latitude' => 'decimal:8',
        'checkin_longitude' => 'decimal:8',
        'checkout_latitude' => 'decimal:8',
        'checkout_longitude' => 'decimal:8',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function scheduledByUser(): BelongsTo { return $this->belongsTo(User::class, 'scheduled_by'); }
    public function assignedToUser(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }

    public function scopeForLead($query, $leadId) { return $query->where('lead_id', $leadId); }
    public function scopeForProject($query, $projectId) { return $query->where('project_id', $projectId); }
    public function scopeUpcoming($query) { return $query->where('scheduled_date', '>=', now()->toDateString())->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED]); }
    public function scopeToday($query) { return $query->whereDate('scheduled_date', now()->toDateString()); }

    public function verifyGpsLocation(float $lat, float $lng, float $projectLat, float $projectLng, int $radiusMeters = 500): bool
    {
        $distance = $this->haversineDistance($lat, $lng, $projectLat, $projectLng);
        return $distance <= $radiusMeters;
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1-$a));
    }
}
