<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Required fields
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:residential,commercial,villa,open_plots',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',

            // Optional basic details
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'logo' => 'nullable|file|image|max:2048', // Accept image files up to 2MB
            'status' => 'nullable|in:upcoming,ongoing,completed,sold_out',
            'description' => 'nullable|string',
            'highlights' => 'nullable|array',
            'highlights.*' => 'string|max:255',

            // RERA
            'rera_number' => 'nullable|string|max:50',
            'rera_valid_until' => 'nullable|date|after:today',

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
            'possession_date' => 'nullable|date|after_or_equal:launch_date',
            'completion_percentage' => 'nullable|integer|between:0,100',

            // Meta
            'is_featured' => 'boolean',
            'is_active' => 'boolean',

            // Images (child table)
            'images' => 'nullable|array',
            'images.*.file' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB
            'images.*.title' => 'nullable|string|max:255',
            'images.*.alt_text' => 'nullable|string|max:255',
            'images.*.type' => 'required|in:gallery,floor_plan,master_plan,brochure,elevation,amenity,other',
            'images.*.sort_order' => 'nullable|integer|min:0',
            'images.*.is_primary' => 'nullable|boolean',

            // Amenities (child table)
            'amenities' => 'nullable|array',
            'amenities.*.name' => 'required|string|max:100',
            'amenities.*.category' => 'required|in:lifestyle,sports,convenience,security,kids,health,green,other',
            'amenities.*.icon' => 'nullable|string|max:100',
            'amenities.*.description' => 'nullable|string',
            'amenities.*.is_highlighted' => 'nullable|boolean',
            'amenities.*.sort_order' => 'nullable|integer|min:0',

            // Towers/Blocks (child table)
            'towers' => 'nullable|array',
            'towers.*.name' => 'required|string|max:100',
            'towers.*.total_floors' => 'required|integer|min:1',
            'towers.*.units_per_floor' => 'nullable|integer|min:1',
            'towers.*.basement_levels' => 'nullable|integer|min:0',
            'towers.*.has_terrace' => 'nullable|boolean',
            'towers.*.status' => 'required|in:upcoming,construction,completed',
            'towers.*.completion_date' => 'nullable|date',
            'towers.*.sort_order' => 'nullable|integer|min:0',

            // Units (child table)
            'units' => 'nullable|array',
            'units.*.name' => 'required|string|max:100',
            'units.*.type' => 'required|in:1bhk,2bhk,3bhk,4bhk,5bhk,studio,penthouse,duplex,shop,office,showroom,plot,villa',
            'units.*.carpet_area_sqft' => 'nullable|numeric|min:0',
            'units.*.built_up_area_sqft' => 'nullable|numeric|min:0',
            'units.*.super_built_up_sqft' => 'nullable|numeric|min:0',
            'units.*.bedrooms' => 'nullable|integer|min:0',
            'units.*.bathrooms' => 'nullable|integer|min:0',
            'units.*.balconies' => 'nullable|integer|min:0',
            'units.*.facing' => 'nullable|in:north,south,east,west,north-east,north-west,south-east,south-west',
            'units.*.floor_plan_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            'units.*.base_price' => 'nullable|numeric|min:0',
            'units.*.total_units' => 'nullable|integer|min:1',
            'units.*.available_units' => 'nullable|integer|min:0',
            'units.*.is_active' => 'nullable|boolean',
            'units.*.sort_order' => 'nullable|integer|min:0',

            // Residential Specifications
            'residential_spec' => 'nullable|array',
            'residential_spec.total_towers' => 'nullable|integer|min:0',
            'residential_spec.total_floors_per_tower' => 'nullable|integer|min:0',
            'residential_spec.total_units' => 'nullable|integer|min:0',
            'residential_spec.units_per_floor' => 'nullable|integer|min:0',
            'residential_spec.basement_levels' => 'nullable|integer|min:0',
            'residential_spec.open_parking_slots' => 'nullable|integer|min:0',
            'residential_spec.covered_parking_slots' => 'nullable|integer|min:0',
            'residential_spec.clubhouse_area_sqft' => 'nullable|numeric|min:0',
            'residential_spec.stilt_parking' => 'nullable|boolean',
            'residential_spec.podium_level' => 'nullable|boolean',

            // Commercial Specifications
            'commercial_spec' => 'nullable|array',
            'commercial_spec.total_floors' => 'nullable|integer|min:0',
            'commercial_spec.total_units' => 'nullable|integer|min:0',
            'commercial_spec.office_units' => 'nullable|integer|min:0',
            'commercial_spec.retail_units' => 'nullable|integer|min:0',
            'commercial_spec.food_court_area_sqft' => 'nullable|numeric|min:0',
            'commercial_spec.common_area_percentage' => 'nullable|numeric|min:0|max:100',
            'commercial_spec.visitor_parking_slots' => 'nullable|integer|min:0',
            'commercial_spec.tenant_parking_slots' => 'nullable|integer|min:0',
            'commercial_spec.green_building_certified' => 'nullable|boolean',
            'commercial_spec.certification_type' => 'nullable|string|max:100',

            // Villa Specifications
            'villa_spec' => 'nullable|array',
            'villa_spec.total_villas' => 'nullable|integer|min:0',
            'villa_spec.villa_types' => 'nullable|string|max:255',
            'villa_spec.floors_per_villa' => 'nullable|integer|min:1',
            'villa_spec.car_parking_per_villa' => 'nullable|integer|min:0',
            'villa_spec.clubhouse_area_sqft' => 'nullable|numeric|min:0',
            'villa_spec.private_garden' => 'nullable|boolean',
            'villa_spec.private_pool' => 'nullable|boolean',
            'villa_spec.servant_quarters' => 'nullable|boolean',
            'villa_spec.gated_community' => 'nullable|boolean',

            // Open Plot Specifications
            'open_plot_spec' => 'nullable|array',
            'open_plot_spec.total_plots' => 'nullable|integer|min:0',
            'open_plot_spec.min_plot_size_sqyds' => 'nullable|numeric|min:0',
            'open_plot_spec.max_plot_size_sqyds' => 'nullable|numeric|min:0',
            'open_plot_spec.park_area_sqft' => 'nullable|numeric|min:0',
            'open_plot_spec.community_hall_sqft' => 'nullable|numeric|min:0',
            'open_plot_spec.underground_drainage' => 'nullable|boolean',
            'open_plot_spec.underground_electricity' => 'nullable|boolean',
            'open_plot_spec.water_supply' => 'nullable|boolean',
            'open_plot_spec.compound_wall' => 'nullable|boolean',
            'open_plot_spec.avenue_plantation' => 'nullable|boolean',
            'open_plot_spec.fencing' => 'nullable|boolean',
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
