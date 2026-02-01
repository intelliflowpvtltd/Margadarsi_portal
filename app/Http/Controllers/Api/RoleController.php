<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Role::query();

        // Filter by company (required for proper scoping)
        if ($request->filled('company_id')) {
            $query->forCompany($request->input('company_id'));
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by system/custom roles
        if ($request->has('is_system')) {
            $query->where('is_system', $request->boolean('is_system'));
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('slug', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // Include soft deleted if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Load relationship counts
        $query->withCount(['permissions', 'users']);

        // Order by hierarchy level (highest authority first)
        $query->orderByHierarchy();

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $roles = $query->paginate($perPage);

        return RoleResource::collection($roles);
    }

    /**
     * Return data needed for the create role form.
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'hierarchy_levels' => Role::HIERARCHY_LEVELS,
            'scopes' => [
                Role::SCOPE_COMPANY => 'Company-wide',
                Role::SCOPE_PROJECT => 'Project-specific',
                Role::SCOPE_DEPARTMENT => 'Department-specific',
            ],
            'department_roles' => Role::DEPARTMENT_ROLES,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Ensure custom roles are not marked as system roles
        $data['is_system'] = false;

        $role = Role::create($data);

        return response()->json([
            'message' => 'Role created successfully.',
            'data' => new RoleResource($role),
        ], 201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): RoleResource
    {
        return new RoleResource(
            $role->loadCount(['permissions', 'users'])
                ->load(['company', 'permissions', 'users'])
        );
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        // Prevent updating system role's critical fields
        $data = $request->validated();

        if ($role->is_system) {
            // System roles: only allow updating description and is_active
            $data = array_intersect_key($data, [
                'description' => true,
                'is_active' => true,
            ]);
        }

        $role->update($data);

        return response()->json([
            'message' => 'Role updated successfully.',
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Remove the specified role (soft delete).
     */
    public function destroy(Role $role): JsonResponse
    {
        // Prevent deleting system roles
        if ($role->is_system) {
            return response()->json([
                'message' => 'System roles cannot be deleted.',
            ], 403);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted role.
     */
    public function restore(int $id): JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);
        $role->restore();

        return response()->json([
            'message' => 'Role restored successfully.',
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Get all default system roles configuration.
     */
    public function systemRoles(): JsonResponse
    {
        return response()->json([
            'data' => Role::SYSTEM_ROLES,
        ]);
    }

    /**
     * Seed system roles for a specific company.
     */
    public function seedSystemRoles(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $companyId = $request->input('company_id');

        // Check if company already has system roles
        $existingCount = Role::forCompany($companyId)->system()->count();

        if ($existingCount > 0) {
            return response()->json([
                'message' => 'Company already has system roles.',
                'existing_count' => $existingCount,
            ], 409);
        }

        Role::createSystemRolesForCompany($companyId);

        $roles = Role::forCompany($companyId)->system()->orderByHierarchy()->get();

        return response()->json([
            'message' => 'System roles created successfully.',
            'data' => RoleResource::collection($roles),
        ], 201);
    }
}
