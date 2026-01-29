<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTowerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_floors' => $this->total_floors,
            'units_per_floor' => $this->units_per_floor,
            'total_units' => $this->total_units,
            'basement_levels' => $this->basement_levels,
            'has_terrace' => $this->has_terrace,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'completion_date' => $this->completion_date?->format('Y-m-d'),
            'sort_order' => $this->sort_order,
        ];
    }
}
