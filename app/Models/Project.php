<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'logo',
        'type',
        'status',
        'description',
        'highlights',
        'rera_number',
        'rera_valid_until',
        'address_line1',
        'address_line2',
        'landmark',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'google_maps_url',
        'total_land_area',
        'land_area_unit',
        'launch_date',
        'possession_date',
        'completion_percentage',
        'is_featured',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'highlights' => 'array',
        'rera_valid_until' => 'date',
        'launch_date' => 'date',
        'possession_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_land_area' => 'decimal:2',
        'completion_percentage' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Project types enum values.
     */
    public const TYPES = [
        'residential' => 'Residential',
        'commercial' => 'Commercial',
        'villa' => 'Villa',
        'open_plots' => 'Open Plots',
    ];

    /**
     * Project status enum values.
     */
    public const STATUSES = [
        'upcoming' => 'Upcoming',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'sold_out' => 'Sold Out',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the company that owns the project.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all images for the project.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProjectImage::class)->where('is_primary', true);
    }

    /**
     * Get residential specifications.
     */
    public function residentialSpec(): HasOne
    {
        return $this->hasOne(ResidentialSpec::class);
    }

    /**
     * Get commercial specifications.
     */
    public function commercialSpec(): HasOne
    {
        return $this->hasOne(CommercialSpec::class);
    }

    /**
     * Get villa specifications.
     */
    public function villaSpec(): HasOne
    {
        return $this->hasOne(VillaSpec::class);
    }

    /**
     * Get open plot specifications.
     */
    public function openPlotSpec(): HasOne
    {
        return $this->hasOne(OpenPlotSpec::class);
    }

    /**
     * Get the specification based on project type.
     */
    public function getSpecificationAttribute()
    {
        return match ($this->type) {
            'residential' => $this->residentialSpec,
            'commercial' => $this->commercialSpec,
            'villa' => $this->villaSpec,
            'open_plots' => $this->openPlotSpec,
            default => null,
        };
    }

    /**
     * Get all towers for the project.
     */
    public function towers(): HasMany
    {
        return $this->hasMany(ProjectTower::class)->orderBy('sort_order');
    }

    /**
     * Get all unit configurations for the project.
     */
    public function units(): HasMany
    {
        return $this->hasMany(ProjectUnit::class)->orderBy('sort_order');
    }

    /**
     * Get all amenities for the project.
     */
    public function amenities(): HasMany
    {
        return $this->hasMany(ProjectAmenity::class)->orderBy('sort_order');
    }

    /**
     * Get all users assigned to this project.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_projects')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    // ==================== ACCESSORS ====================

    /**
     * Get full address.
     */
    public function getFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->landmark,
            $this->city,
            $this->state,
            $this->pincode,
        ]);

        return count($parts) > 0 ? implode(', ', $parts) : null;
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): ?string
    {
        if (!$this->type) return null;
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): ?string
    {
        if (!$this->status) return null;
        return self::STATUSES[$this->status] ?? $this->status;
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured projects.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for projects by city.
     */
    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }
}
