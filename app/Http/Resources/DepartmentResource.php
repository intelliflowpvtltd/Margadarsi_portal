<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'project_id' => $this->project_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'scope' => $this->scope,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Counts (when loaded via withCount)
            'users_count' => $this->whenCounted('users'),
            'roles_count' => $this->whenCounted('roles'),

            // Relations (when loaded)
            'company' => new CompanyResource($this->whenLoaded('company')),
            'project' => new ProjectResource($this->whenLoaded('project')),
        ];
    }
}
