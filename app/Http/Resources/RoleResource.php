<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'project_id' => $this->project_id,
            'department_id' => $this->department_id,
            'scope' => $this->scope,
            'requires_project_assignment' => $this->scope !== 'company', // True for project/department scope roles
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'hierarchy_level' => $this->hierarchy_level,
            'hierarchy_label' => $this->hierarchy_label,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Counts (when loaded via withCount)
            'permissions_count' => $this->whenCounted('permissions'),
            'users_count' => $this->whenCounted('users'),

            // Relations (when loaded)
            'company' => new CompanyResource($this->whenLoaded('company')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
