<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'company_id' => $this->company_id,

            // Basic Details
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'description' => $this->description,
            'highlights' => $this->highlights,

            // RERA
            'rera_number' => $this->rera_number,
            'rera_valid_until' => $this->rera_valid_until?->format('Y-m-d'),

            // Location
            'location' => [
                'address_line1' => $this->address_line1,
                'address_line2' => $this->address_line2,
                'landmark' => $this->landmark,
                'city' => $this->city,
                'state' => $this->state,
                'pincode' => $this->pincode,
                'full_address' => $this->full_address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'google_maps_url' => $this->google_maps_url,
            ],

            // Land
            'land' => [
                'total_area' => $this->total_land_area,
                'unit' => $this->land_area_unit,
            ],

            // Timeline
            'timeline' => [
                'launch_date' => $this->launch_date?->format('Y-m-d'),
                'possession_date' => $this->possession_date?->format('Y-m-d'),
                'completion_percentage' => $this->completion_percentage,
            ],

            // Meta
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,

            // Relations (when loaded)
            'company' => new CompanyResource($this->whenLoaded('company')),
            'primary_image' => $this->whenLoaded('primaryImage', fn() => [
                'id' => $this->primaryImage->id,
                'path' => $this->primaryImage->image_path,
                'title' => $this->primaryImage->title,
            ]),
            'images' => ProjectImageResource::collection($this->whenLoaded('images')),
            'specification' => $this->whenLoaded(
                $this->getSpecificationRelation(),
                fn() => $this->formatSpecification()
            ),
            'towers' => ProjectTowerResource::collection($this->whenLoaded('towers')),
            'units' => ProjectUnitResource::collection($this->whenLoaded('units')),
            'amenities' => ProjectAmenityResource::collection($this->whenLoaded('amenities')),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get the specification relation name based on type.
     */
    private function getSpecificationRelation(): string
    {
        return match ($this->type) {
            'residential' => 'residentialSpec',
            'commercial' => 'commercialSpec',
            'villa' => 'villaSpec',
            'open_plots' => 'openPlotSpec',
            default => 'residentialSpec',
        };
    }

    /**
     * Format specification based on project type.
     */
    private function formatSpecification(): ?array
    {
        $spec = $this->specification;
        if (!$spec) return null;

        return match ($this->type) {
            'residential' => [
                'total_towers' => $spec->total_towers,
                'total_floors_per_tower' => $spec->total_floors_per_tower,
                'total_units' => $spec->total_units,
                'units_per_floor' => $spec->units_per_floor,
                'basement_levels' => $spec->basement_levels,
                'parking' => [
                    'stilt' => $spec->stilt_parking,
                    'open_slots' => $spec->open_parking_slots,
                    'covered_slots' => $spec->covered_parking_slots,
                ],
                'clubhouse' => [
                    'floors' => $spec->clubhouse_floors,
                    'area_sqft' => $spec->clubhouse_area_sqft,
                ],
                'podium' => [
                    'has_podium' => $spec->podium_level,
                    'area_sqft' => $spec->podium_area_sqft,
                ],
            ],
            'commercial' => [
                'total_floors' => $spec->total_floors,
                'total_units' => $spec->total_units,
                'office_units' => $spec->office_units,
                'retail_units' => $spec->retail_units,
                'food_court_area_sqft' => $spec->food_court_area_sqft,
                'common_area_percentage' => $spec->common_area_percentage,
                'parking' => [
                    'basement_levels' => $spec->basement_levels,
                    'visitor_slots' => $spec->visitor_parking_slots,
                    'tenant_slots' => $spec->tenant_parking_slots,
                ],
                'certification' => [
                    'green_certified' => $spec->green_building_certified,
                    'type' => $spec->certification_type,
                ],
            ],
            'villa' => [
                'total_villas' => $spec->total_villas,
                'villa_types' => $spec->villa_types,
                'floors_per_villa' => $spec->floors_per_villa,
                'plot_sizes_sqft' => $spec->plot_sizes_sqft,
                'built_up_sizes_sqft' => $spec->built_up_sizes_sqft,
                'features' => [
                    'private_garden' => $spec->private_garden,
                    'private_pool' => $spec->private_pool,
                    'servant_quarters' => $spec->servant_quarters,
                ],
                'car_parking_per_villa' => $spec->car_parking_per_villa,
                'gated_community' => $spec->gated_community,
                'clubhouse_area_sqft' => $spec->clubhouse_area_sqft,
            ],
            'open_plots' => [
                'total_plots' => $spec->total_plots,
                'plot_sizes' => $spec->plot_sizes,
                'min_plot_size_sqyds' => $spec->min_plot_size_sqyds,
                'max_plot_size_sqyds' => $spec->max_plot_size_sqyds,
                'road_width_feet' => $spec->road_width_feet,
                'infrastructure' => [
                    'underground_drainage' => $spec->underground_drainage,
                    'underground_electricity' => $spec->underground_electricity,
                    'water_supply' => $spec->water_supply,
                    'compound_wall' => $spec->compound_wall,
                    'avenue_plantation' => $spec->avenue_plantation,
                    'fencing' => $spec->fencing,
                ],
                'common_areas' => [
                    'park_area_sqft' => $spec->park_area_sqft,
                    'community_hall_sqft' => $spec->community_hall_sqft,
                ],
            ],
            default => null,
        };
    }
}
