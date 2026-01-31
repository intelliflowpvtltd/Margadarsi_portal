<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query();

        // Eager load relations
        $query->with(['company', 'role']);

        // Filter by company (required for proper scoping)
        if ($request->filled('company_id')) {
            $query->forCompany($request->input('company_id'));
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->withRole($request->input('role_id'));
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by project assignment
        if ($request->filled('project_id')) {
            $query->assignedToProject($request->input('project_id'));
        }

        // Search by name or email
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Include soft deleted if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // With projects count
        if ($request->boolean('with_projects_count')) {
            $query->withCount('projects');
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['first_name', 'last_name', 'email', 'created_at', 'updated_at', 'last_login_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $users = $query->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Extract project_ids if provided
        $projectIds = $data['project_ids'] ?? [];
        unset($data['project_ids']);

        // Use transaction for multi-step operation
        $user = DB::transaction(function () use ($data, $projectIds) {
            $user = User::create($data);

            // Assign to projects if provided
            if (!empty($projectIds)) {
                foreach ($projectIds as $projectId) {
                    $user->assignToProject($projectId);
                }
            }

            return $user;
        });

        return response()->json([
            'message' => 'User created successfully.',
            'data' => new UserResource($user->load(['company', 'role', 'projects'])),
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user->load(['company', 'role', 'projects']));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => new UserResource($user->load(['company', 'role'])),
        ]);
    }

    /**
     * Remove the specified user (soft delete).
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'message' => 'User restored successfully.',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Get all projects assigned to a user.
     */
    public function projects(User $user): JsonResponse
    {
        $projects = $user->projects()
            ->with(['company', 'primaryImage'])
            ->get();

        return response()->json([
            'data' => $projects,
        ]);
    }

    /**
     * Assign user to projects.
     */
    public function assignProjects(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'project_ids' => ['required', 'array'],
            'project_ids.*' => [
                'exists:projects,id',
                // Ensure projects belong to same company as user
                \Illuminate\Validation\Rule::exists('projects', 'id')
                    ->where('company_id', $user->company_id),
            ],
        ]);

        $projectIds = $request->input('project_ids');
        $assignedBy = request()->user()?->id; // Get from authenticated user

        foreach ($projectIds as $projectId) {
            $user->assignToProject($projectId, $assignedBy);
        }

        return response()->json([
            'message' => 'User assigned to projects successfully.',
            'data' => new UserResource($user->load('projects')),
        ]);
    }

    /**
     * Remove user from a project.
     */
    public function removeProject(User $user, int $projectId): JsonResponse
    {
        // Verify project exists and belongs to same company
        $project = \App\Models\Project::where('id', $projectId)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $user->removeFromProject($projectId);

        return response()->json([
            'message' => 'User removed from project successfully.',
        ]);
    }
}
