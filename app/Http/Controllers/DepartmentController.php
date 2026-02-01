<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display departments index page.
     */
    public function index(Request $request): View
    {
        // Return Blade view for web requests
        return view('departments.index');
    }

    /**
     * Show create department form.
     */
    public function create(): View
    {
        return view('departments.create');
    }

    /**
     * Show a specific department.
     */
    public function show(int $id): View
    {
        $department = Department::findOrFail($id);
        return view('departments.show', compact('department'));
    }

    /**
     * Show edit department form.
     */
    public function edit(int $id): View
    {
        $department = Department::findOrFail($id);
        return view('departments.edit', compact('department'));
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
            $rules['company_id'] = 'required|exists:companies,id';
            $rules['project_id'] = 'nullable';
        } else {
            $rules['project_id'] = 'required|exists:projects,id';
            $rules['company_id'] = 'nullable|exists:companies,id';
        }
        
        $validated = $request->validate($rules);
        
        // Check uniqueness based on department level
        if ($isCompanyLevel) {
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
            
            $validated['project_id'] = null;
            
            if (empty($validated['company_id']) && !empty($request->input('project_id'))) {
                $project = Project::find($request->input('project_id'));
                if ($project) {
                    $validated['company_id'] = $project->company_id;
                }
            }
        } else {
            $exists = Department::where('project_id', $validated['project_id'])
                ->where('slug', $validated['slug'])
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'message' => 'A department with this slug already exists for this project.',
                    'errors' => ['slug' => ['Department slug must be unique within the project.']],
                ], 422);
            }
            
            if (empty($validated['company_id'])) {
                $project = Project::find($validated['project_id']);
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
