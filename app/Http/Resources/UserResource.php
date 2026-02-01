<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'role_id' => $this->role_id,
            'department_id' => $this->department_id,

            // Personal Information
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,

            // Employee fields
            'employee_code' => $this->employee_code,
            'designation' => $this->designation,
            'reports_to' => $this->reports_to,

            // Status
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toISOString(),
            'email_verified_at' => $this->email_verified_at?->toISOString(),

            // Relationships (optional, loaded when needed)
            'company' => new CompanyResource($this->whenLoaded('company')),
            'role' => new RoleResource($this->whenLoaded('role')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'manager' => new UserResource($this->whenLoaded('manager')),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'projects_count' => $this->when(isset($this->projects_count), $this->projects_count),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
