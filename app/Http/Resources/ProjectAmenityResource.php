<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectAmenityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'icon' => $this->icon,
            'description' => $this->description,
            'is_highlighted' => $this->is_highlighted,
            'sort_order' => $this->sort_order,
        ];
    }
}
