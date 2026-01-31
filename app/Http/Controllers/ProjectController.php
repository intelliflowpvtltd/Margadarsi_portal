<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects (web route).
     */
    public function index(Request $request)
    {
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getProjectsJson($request);
        }

        // Otherwise return view
        return view('projects.index');
    }

    /**
     * Get projects as JSON for AJAX requests.
     */
    private function getProjectsJson(Request $request): JsonResponse
    {
        $query = Project::with('company');

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'ilike', '%' . $request->input('city') . '%');
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name, city, or RERA
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('city', 'ilike', "%{$search}%")
                    ->orWhere('rera_number', 'ilike', "%{$search}%");
            });
        }

        // Include soft deleted if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['name', 'city', 'type', 'status', 'launch_date', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->input('per_page', 12), 100);
        $projects = $query->paginate($perPage);

        return response()->json([
            'data' => $projects->items(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'from' => $projects->firstItem(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'to' => $projects->lastItem(),
                'total' => $projects->total(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        return view('projects.create', compact('companies'));
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('projects/logos', 'public');
            $data['logo'] = '/storage/' . $logoPath;
        }

        $project = Project::create($data);

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => $project->load('company'),
        ], 201);
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, Project $project)
    {
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $project->load('company'),
            ]);
        }

        // Load relationship counts for the view
        $project->loadCount(['towers', 'units', 'amenities', 'users', 'images']);
        $project->load('company');

        // Otherwise return view
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        return view('projects.edit', compact('project', 'companies'));
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($project->logo) {
                $oldPath = str_replace('/storage/', '', $project->logo);
                Storage::disk('public')->delete($oldPath);
            }

            $logoPath = $request->file('logo')->store('projects/logos', 'public');
            $data['logo'] = '/storage/' . $logoPath;
        }

        $project->update($data);

        // Refresh to get latest data
        $project->fresh()->load('company');

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => $project,
        ]);
    }

    /**
     * Remove the specified project (soft delete).
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted project.
     */
    public function restore(int $id): JsonResponse
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->restore();

        return response()->json([
            'message' => 'Project restored successfully.',
            'data' => $project->load('company'),
        ]);
    }

    /**
     * Permanently delete a project.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $project = Project::withTrashed()->findOrFail($id);

        // Delete logo if exists
        if ($project->logo) {
            $oldPath = str_replace('/storage/', '', $project->logo);
            Storage::disk('public')->delete($oldPath);
        }

        $project->forceDelete();

        return response()->json([
            'message' => 'Project permanently deleted.',
        ]);
    }
}
