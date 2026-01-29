<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Basic Info
        'name',
        'legal_name',
        'tagline',
        'description',
        'logo',
        'favicon',

        // Registration (India-specific)
        'pan_number',
        'gstin',
        'cin',
        'rera_number',
        'incorporation_date',

        // Contact Information
        'email',
        'phone',
        'alternate_phone',
        'whatsapp',
        'website',

        // Address - Registered Office
        'registered_address_line1',
        'registered_address_line2',
        'registered_city',
        'registered_state',
        'registered_pincode',
        'registered_country',

        // Address - Corporate/Branch Office
        'corporate_address_line1',
        'corporate_address_line2',
        'corporate_city',
        'corporate_state',
        'corporate_pincode',

        // Social Media
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'twitter_url',
        'youtube_url',

        // Status
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'incorporation_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the full registered address.
     */
    public function getRegisteredFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->registered_address_line1,
            $this->registered_address_line2,
            $this->registered_city,
            $this->registered_state,
            $this->registered_pincode,
            $this->registered_country,
        ]);

        return count($parts) > 0 ? implode(', ', $parts) : null;
    }

    /**
     * Get the full corporate address.
     */
    public function getCorporateFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->corporate_address_line1,
            $this->corporate_address_line2,
            $this->corporate_city,
            $this->corporate_state,
            $this->corporate_pincode,
        ]);

        return count($parts) > 0 ? implode(', ', $parts) : null;
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive companies.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get all projects for the company.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get all roles for the company.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get all users for the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
