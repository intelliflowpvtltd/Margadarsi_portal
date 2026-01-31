<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display departments for a project.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Department::with(['project', 'roles', 'users']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                    ->orWhere('description', 'ilike', "%{$request->search}%");
            });
        }

        $departments = $query->orderBy('name')->get();

        // Add counts
        $departments->each(function ($dept) {
            $dept->roles_count = $dept->roles()->count();
            $dept->users_count = $dept->users()->count();
        });

        return response()->json([
            'data' => $departments,
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check if department slug already exists for this project
        $exists = Department::where('project_id', $validated['project_id'])
            ->where('slug', $validated['slug'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A department with this slug already exists for this project.',
                'errors' => ['slug' => ['Department slug must be unique within the project.']],
            ], 422);
        }

        $department = Department::create($validated);

        return response()->json([
            'message' => 'Department created successfully.',
            'data' => $department->load(['project', 'roles', 'users']),
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show(int $id): JsonResponse
    {
        $department = Department::with([
            'project',
            'roles.users',
            'users.role',
        ])->findOrFail($id);

        return response()->json([
            'data' => $department,
        ]);
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return response()->json([
            'message' => 'Department updated successfully.',
            'data' => $department->load(['project', 'roles', 'users']),
        ]);
    }

    /**
     * Remove the specified department.
     */
    public function destroy(int $id): JsonResponse
    {
        $department = Department::findOrFail($id);

        // Check if department has users
        if ($department->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete department with active users. Please reassign users first.',
            ], 422);
        }

        // Check if department has roles
        if ($department->roles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete department with existing roles.',
            ], 422);
        }

        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully.',
        ]);
    }

    /**
     * Get department statistics.
     */
    public function stats(int $id): JsonResponse
    {
        $department = Department::with(['roles', 'users'])->findOrFail($id);

        $stats = [
            'total_roles' => $department->roles()->count(),
            'active_roles' => $department->roles()->active()->count(),
            'total_users' => $department->users()->count(),
            'active_users' => $department->users()->active()->count(),
            'roles_by_hierarchy' => $department->roles()
                ->selectRaw('hierarchy_level, count(*) as count')
                ->groupBy('hierarchy_level')
                ->orderBy('hierarchy_level')
                ->get(),
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }
}
