<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ResidentialSpec;
use App\Models\CommercialSpec;
use App\Models\VillaSpec;
use App\Models\OpenPlotSpec;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Project::query();

        // Eager load relations
        $query->with(['company', 'primaryImage']);

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->ofType($request->input('type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->ofStatus($request->input('status'));
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->inCity($request->input('city'));
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Search by name
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
        $perPage = min($request->input('per_page', 15), 100);
        $projects = $query->paginate($perPage);

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        // Create type-specific specification if provided
        $this->createSpecification($project, $request);

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => new ProjectResource($project->load(['company', 'primaryImage'])),
        ], 201);
    }

    /**
     * Display the specified project with all relations.
     */
    public function show(Project $project): ProjectResource
    {
        // Load all relations for detailed view
        $project->load([
            'company',
            'images',
            'towers',
            'units',
            'amenities',
        ]);

        // Load type-specific spec
        match ($project->type) {
            'residential' => $project->load('residentialSpec'),
            'commercial' => $project->load('commercialSpec'),
            'villa' => $project->load('villaSpec'),
            'open_plots' => $project->load('openPlotSpec'),
            default => null,
        };

        return new ProjectResource($project);
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => new ProjectResource($project->load(['company', 'primaryImage'])),
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
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Permanently delete a project.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->forceDelete();

        return response()->json([
            'message' => 'Project permanently deleted.',
        ]);
    }

    /**
     * Update project specification.
     */
    public function updateSpecification(Request $request, Project $project): JsonResponse
    {
        $specData = $request->all();

        $spec = match ($project->type) {
            'residential' => $project->residentialSpec()->updateOrCreate(
                ['project_id' => $project->id],
                $specData
            ),
            'commercial' => $project->commercialSpec()->updateOrCreate(
                ['project_id' => $project->id],
                $specData
            ),
            'villa' => $project->villaSpec()->updateOrCreate(
                ['project_id' => $project->id],
                $specData
            ),
            'open_plots' => $project->openPlotSpec()->updateOrCreate(
                ['project_id' => $project->id],
                $specData
            ),
            default => null,
        };

        return response()->json([
            'message' => 'Specification updated successfully.',
            'data' => $spec,
        ]);
    }

    /**
     * Create type-specific specification.
     */
    private function createSpecification(Project $project, Request $request): void
    {
        // This will be called if spec data is included in project creation
        // For now, we just create empty spec record
        match ($project->type) {
            'residential' => ResidentialSpec::create(['project_id' => $project->id]),
            'commercial' => CommercialSpec::create(['project_id' => $project->id]),
            'villa' => VillaSpec::create(['project_id' => $project->id]),
            'open_plots' => OpenPlotSpec::create(['project_id' => $project->id]),
            default => null,
        };
    }
}
