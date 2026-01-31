<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Will be controlled by middleware/policies later
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Info
            'name' => 'sometimes|required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'logo' => 'nullable|file|image|max:2048', // Accept image files up to 2MB
            'favicon' => 'nullable|file|image|max:1024', // Accept image files up to 1MB

            // Registration (India-specific)
            'pan_number' => 'nullable|string|size:10|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'gstin' => 'nullable|string|size:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'cin' => 'nullable|string|max:21',
            'rera_number' => 'nullable|string|max:50',
            'incorporation_date' => 'nullable|date|before_or_equal:today',

            // Contact Information
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:15|regex:/^[6-9]\d{9}$/',
            'alternate_phone' => 'nullable|string|max:15|regex:/^[6-9]\d{9}$/',
            'whatsapp' => 'nullable|string|max:15|regex:/^[6-9]\d{9}$/',
            'website' => 'nullable|url|max:255',

            // Address - Registered Office
            'registered_address_line1' => 'nullable|string|max:255',
            'registered_address_line2' => 'nullable|string|max:255',
            'registered_city' => 'nullable|string|max:100',
            'registered_state' => 'nullable|string|max:100',
            'registered_pincode' => 'nullable|string|size:6|regex:/^[1-9][0-9]{5}$/',
            'registered_country' => 'nullable|string|max:100',

            // Address - Corporate/Branch Office
            'corporate_address_line1' => 'nullable|string|max:255',
            'corporate_address_line2' => 'nullable|string|max:255',
            'corporate_city' => 'nullable|string|max:100',
            'corporate_state' => 'nullable|string|max:100',
            'corporate_pincode' => 'nullable|string|size:6|regex:/^[1-9][0-9]{5}$/',

            // Social Media
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',

            // Status
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pan_number.regex' => 'Invalid PAN number format. Expected: AAAAA9999A',
            'gstin.regex' => 'Invalid GSTIN format. Expected: 22AAAAA0000A1Z5',
            'phone.regex' => 'Invalid Indian mobile number. Must start with 6-9 and be 10 digits.',
            'alternate_phone.regex' => 'Invalid Indian mobile number. Must start with 6-9 and be 10 digits.',
            'whatsapp.regex' => 'Invalid Indian mobile number. Must start with 6-9 and be 10 digits.',
            'registered_pincode.regex' => 'Invalid PIN code. Must be 6 digits starting with 1-9.',
            'corporate_pincode.regex' => 'Invalid PIN code. Must be 6 digits starting with 1-9.',
        ];
    }
}
