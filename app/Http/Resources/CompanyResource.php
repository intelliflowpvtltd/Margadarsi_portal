<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Basic Info
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'logo' => $this->logo,
            'favicon' => $this->favicon,

            // Registration (India-specific)
            'pan_number' => $this->pan_number,
            'gstin' => $this->gstin,
            'cin' => $this->cin,
            'rera_number' => $this->rera_number,
            'incorporation_date' => $this->incorporation_date?->format('Y-m-d'),

            // Contact Information
            'email' => $this->email,
            'phone' => $this->phone,
            'alternate_phone' => $this->alternate_phone,
            'whatsapp' => $this->whatsapp,
            'website' => $this->website,

            // Address - Registered Office
            'registered_address' => [
                'line1' => $this->registered_address_line1,
                'line2' => $this->registered_address_line2,
                'city' => $this->registered_city,
                'state' => $this->registered_state,
                'pincode' => $this->registered_pincode,
                'country' => $this->registered_country,
                'full_address' => $this->registered_full_address,
            ],

            // Address - Corporate/Branch Office
            'corporate_address' => [
                'line1' => $this->corporate_address_line1,
                'line2' => $this->corporate_address_line2,
                'city' => $this->corporate_city,
                'state' => $this->corporate_state,
                'pincode' => $this->corporate_pincode,
                'full_address' => $this->corporate_full_address,
            ],

            // Social Media
            'social_media' => [
                'facebook' => $this->facebook_url,
                'instagram' => $this->instagram_url,
                'linkedin' => $this->linkedin_url,
                'twitter' => $this->twitter_url,
                'youtube' => $this->youtube_url,
            ],

            // Status & Timestamps
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
