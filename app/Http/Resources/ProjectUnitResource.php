<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'area' => [
                'carpet_sqft' => $this->carpet_area_sqft,
                'built_up_sqft' => $this->built_up_area_sqft,
                'super_built_up_sqft' => $this->super_built_up_sqft,
                'plot_sqyds' => $this->plot_area_sqyds,
            ],
            'configuration' => [
                'bedrooms' => $this->bedrooms,
                'bathrooms' => $this->bathrooms,
                'balconies' => $this->balconies,
                'facing' => $this->facing,
            ],
            'floor_plan_image' => $this->floor_plan_image,
            'pricing' => [
                'on_request' => $this->price_on_request,
                'base_price' => $this->base_price,
                'price_per_sqft' => $this->price_per_sqft,
            ],
            'availability' => [
                'total' => $this->total_units,
                'available' => $this->available_units,
                'booked' => $this->booked_units,
                'percentage' => $this->availability_percentage,
            ],
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
