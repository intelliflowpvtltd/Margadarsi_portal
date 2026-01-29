<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Basic details (sometimes for updates)
            'company_id' => 'sometimes|required|exists:companies,id',
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:residential,commercial,villa,open_plots',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:100',

            // Optional fields
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($this->project)],
            'logo' => 'nullable|string|max:255',
            'status' => 'nullable|in:upcoming,ongoing,completed,sold_out',
            'description' => 'nullable|string',
            'highlights' => 'nullable|array',
            'highlights.*' => 'string|max:255',

            // RERA
            'rera_number' => 'nullable|string|max:50',
            'rera_valid_until' => 'nullable|date',

            // Location
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|size:6|regex:/^[1-9][0-9]{5}$/',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_maps_url' => 'nullable|url|max:500',

            // Land
            'total_land_area' => 'nullable|numeric|min:0',
            'land_area_unit' => 'nullable|in:acres,sqft,sqm,sqyds',

            // Timeline
            'launch_date' => 'nullable|date',
            'possession_date' => 'nullable|date',
            'completion_percentage' => 'nullable|integer|between:0,100',

            // Meta
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'pincode.regex' => 'Invalid PIN code. Must be 6 digits starting with 1-9.',
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }
}
