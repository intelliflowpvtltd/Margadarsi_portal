<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $project = $this->route('project');
        $type = $project?->type;

        // Base rules for all types
        $rules = [];

        // Type-specific validation rules
        return match ($type) {
            'residential' => $this->residentialRules(),
            'commercial' => $this->commercialRules(),
            'villa' => $this->villaRules(),
            'open_plots' => $this->openPlotRules(),
            default => $rules,
        };
    }

    private function residentialRules(): array
    {
        return [
            'total_towers' => 'nullable|integer|min:1',
            'total_floors_per_tower' => 'nullable|integer|min:1',
            'total_units' => 'nullable|integer|min:0',
            'units_per_floor' => 'nullable|integer|min:1',
            'basement_levels' => 'nullable|integer|min:0',
            'stilt_parking' => 'nullable|boolean',
            'open_parking_slots' => 'nullable|integer|min:0',
            'covered_parking_slots' => 'nullable|integer|min:0',
            'clubhouse_floors' => 'nullable|integer|min:0',
            'clubhouse_area_sqft' => 'nullable|numeric|min:0',
            'podium_level' => 'nullable|boolean',
            'podium_area_sqft' => 'nullable|numeric|min:0',
        ];
    }

    private function commercialRules(): array
    {
        return [
            'total_floors' => 'nullable|integer|min:1',
            'total_units' => 'nullable|integer|min:0',
            'office_units' => 'nullable|integer|min:0',
            'retail_units' => 'nullable|integer|min:0',
            'food_court_area_sqft' => 'nullable|numeric|min:0',
            'common_area_percentage' => 'nullable|numeric|min:0|max:100',
            'basement_levels' => 'nullable|integer|min:0',
            'visitor_parking_slots' => 'nullable|integer|min:0',
            'tenant_parking_slots' => 'nullable|integer|min:0',
            'green_building_certified' => 'nullable|boolean',
            'certification_type' => 'nullable|string|max:50|in:LEED,IGBC,GRIHA',
        ];
    }

    private function villaRules(): array
    {
        return [
            'total_villas' => 'nullable|integer|min:1',
            'villa_types' => 'nullable|integer|min:1',
            'floors_per_villa' => 'nullable|integer|min:1',
            'plot_sizes_sqft' => 'nullable|array',
            'plot_sizes_sqft.*' => 'numeric|min:0',
            'built_up_sizes_sqft' => 'nullable|array',
            'built_up_sizes_sqft.*' => 'numeric|min:0',
            'private_garden' => 'nullable|boolean',
            'private_pool' => 'nullable|boolean',
            'servant_quarters' => 'nullable|boolean',
            'car_parking_per_villa' => 'nullable|integer|min:0',
            'gated_community' => 'nullable|boolean',
            'clubhouse_area_sqft' => 'nullable|numeric|min:0',
        ];
    }

    private function openPlotRules(): array
    {
        return [
            'total_plots' => 'nullable|integer|min:1',
            'plot_sizes' => 'nullable|array',
            'min_plot_size_sqyds' => 'nullable|numeric|min:0',
            'max_plot_size_sqyds' => 'nullable|numeric|min:0|gte:min_plot_size_sqyds',
            'road_width_feet' => 'nullable|array',
            'road_width_feet.*' => 'integer|min:0',
            'underground_drainage' => 'nullable|boolean',
            'underground_electricity' => 'nullable|boolean',
            'water_supply' => 'nullable|boolean',
            'compound_wall' => 'nullable|boolean',
            'avenue_plantation' => 'nullable|boolean',
            'fencing' => 'nullable|boolean',
            'park_area_sqft' => 'nullable|numeric|min:0',
            'community_hall_sqft' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'max_plot_size_sqyds.gte' => 'Maximum plot size must be greater than or equal to minimum plot size.',
            'common_area_percentage.max' => 'Common area percentage cannot exceed 100%.',
            'certification_type.in' => 'Certification type must be one of: LEED, IGBC, GRIHA.',
        ];
    }
}
