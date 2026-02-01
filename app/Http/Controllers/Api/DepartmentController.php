<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     * 
     * Supports filtering by:
     * - company_id: Filters both company-level depts (direct) and project-level (via project)
     * - project_id: Filters project-level departments only
     * - scope: 'company' or 'project' to filter by department level
     */
    public function index(Request $request): JsonResponse
    {
        $query = Department::with(['company', 'project', 'roles', 'users']);

        // Filter by project (only for project-level departments)
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        // Filter by company (includes both company-level and project-level departments)
        if ($request->filled('company_id')) {
            $companyId = $request->input('company_id');
            $query->where(function ($q) use ($companyId) {
                // Company-level departments (direct company_id)
                $q->where('company_id', $companyId)
                  // Project-level departments (via project relationship)
                  ->orWhereHas('project', function ($pq) use ($companyId) {
                      $pq->where('company_id', $companyId);
                  });
            });
        }

        // Filter by scope (company-level vs project-level)
        if ($request->filled('scope')) {
            if ($request->input('scope') === 'company') {
                $query->whereIn('slug', Department::COMPANY_LEVEL_DEPARTMENTS);
            } elseif ($request->input('scope') === 'project') {
                $query->whereIn('slug', Department::PROJECT_LEVEL_DEPARTMENTS);
            }
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // Filter by slug/type
        if ($request->filled('slug')) {
            $query->where('slug', $request->input('slug'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc');
        $allowedSorts = ['name', 'slug', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        $departments = $query->get();

        // Add counts and computed fields
        $departments->each(function ($dept) {
            $dept->roles_count = $dept->roles()->count();
            $dept->users_count = $dept->users()->count();
            $dept->is_company_level = in_array($dept->slug, Department::COMPANY_LEVEL_DEPARTMENTS);
            $dept->scope = $dept->is_company_level ? 'company' : 'project';
        });

        return response()->json([
            'data' => $departments,
        ]);
    }

    /**
     * Store a newly created department.
     * 
     * Management departments are company-level (no project_id required).
     * Sales, Pre-Sales, External departments are project-level (project_id required).
     */
    public function store(Request $request): JsonResponse
    {
        $slug = $request->input('slug');
        $isCompanyLevel = in_array($slug, Department::COMPANY_LEVEL_DEPARTMENTS);
        
        // Validation rules depend on department type
        $rules = [
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
        
        if ($isCompanyLevel) {
            // Company-level departments require company_id, not project_id
            $rules['company_id'] = 'required|exists:companies,id';
            $rules['project_id'] = 'nullable';
        } else {
            // Project-level departments require project_id
            $rules['project_id'] = 'required|exists:projects,id';
            $rules['company_id'] = 'nullable|exists:companies,id';
        }
        
        $validated = $request->validate($rules);
        
        // Check uniqueness based on department level
        if ($isCompanyLevel) {
            // Company-level: unique per company
            $exists = Department::where('company_id', $validated['company_id'] ?? null)
                ->whereNull('project_id')
                ->where('slug', $validated['slug'])
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'message' => 'A department with this slug already exists for this company.',
                    'errors' => ['slug' => ['Department slug must be unique within the company.']],
                ], 422);
            }
            
            // Ensure project_id is null for company-level departments
            $validated['project_id'] = null;
            
            // Set company_id from project if not provided
            if (empty($validated['company_id']) && !empty($request->input('project_id'))) {
                $project = \App\Models\Project::find($request->input('project_id'));
                if ($project) {
                    $validated['company_id'] = $project->company_id;
                }
            }
        } else {
            // Project-level: unique per project
            $exists = Department::where('project_id', $validated['project_id'])
                ->where('slug', $validated['slug'])
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'message' => 'A department with this slug already exists for this project.',
                    'errors' => ['slug' => ['Department slug must be unique within the project.']],
                ], 422);
            }
            
            // Set company_id from project for project-level departments
            if (empty($validated['company_id'])) {
                $project = \App\Models\Project::find($validated['project_id']);
                if ($project) {
                    $validated['company_id'] = $project->company_id;
                }
            }
        }

        $department = Department::create($validated);

        return response()->json([
            'message' => 'Department created successfully.',
            'data' => $department->load(['company', 'project', 'roles', 'users']),
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show(int $id): JsonResponse
    {
        $department = Department::with([
            'project.company',
            'roles' => function ($query) {
                $query->withCount('users');
            },
            'users.role',
        ])->findOrFail($id);

        // Add counts
        $department->roles_count = $department->roles()->count();
        $department->users_count = $department->users()->count();

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

        // Check slug uniqueness if being updated
        if ($request->filled('slug') && $request->input('slug') !== $department->slug) {
            $exists = Department::where('project_id', $department->project_id)
                ->where('slug', $request->input('slug'))
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'A department with this slug already exists for this project.',
                    'errors' => ['slug' => ['Department slug must be unique within the project.']],
                ], 422);
            }

            $validated['slug'] = $request->input('slug');
        }

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
                'message' => 'Cannot delete department with existing roles. Please delete or reassign roles first.',
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
            'active_roles' => $department->roles()->where('is_active', true)->count(),
            'total_users' => $department->users()->count(),
            'active_users' => $department->users()->where('is_active', true)->count(),
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
