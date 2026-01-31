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
        try {
            $project = \DB::transaction(function () use ($request) {
                $data = $request->validated();
                
                // Remove images from main data array (handled separately)
                $imagesData = $data['images'] ?? [];
                unset($data['images']);

                // Handle logo upload
                if ($request->hasFile('logo')) {
                    $logoPath = $request->file('logo')->store('projects/logos', 'public');
                    $data['logo'] = '/storage/' . $logoPath;
                }

                // Create project
                $project = Project::create($data);

                // Handle project images
                if (!empty($imagesData)) {
                    foreach ($imagesData as $imageData) {
                        if (isset($imageData['file'])) {
                            // Upload image
                            $imagePath = $imageData['file']->store('projects/images', 'public');
                            
                            // Create image record
                            $project->images()->create([
                                'image_path' => '/storage/' . $imagePath,
                                'title' => $imageData['title'] ?? null,
                                'alt_text' => $imageData['alt_text'] ?? null,
                                'type' => $imageData['type'],
                                'sort_order' => $imageData['sort_order'] ?? 0,
                                'is_primary' => isset($imageData['is_primary']) && $imageData['is_primary'] == '1',
                            ]);
                        }
                    }
                }

                // Handle project amenities
                if (!empty($data['amenities'])) {
                    foreach ($data['amenities'] as $amenityData) {
                        $project->amenities()->create([
                            'name' => $amenityData['name'],
                            'category' => $amenityData['category'],
                            'icon' => $amenityData['icon'] ?? null,
                            'description' => $amenityData['description'] ?? null,
                            'is_highlighted' => isset($amenityData['is_highlighted']) && $amenityData['is_highlighted'] == '1',
                            'sort_order' => $amenityData['sort_order'] ?? 0,
                        ]);
                    }
                }
                unset($data['amenities']);

                // Handle project towers
                if (!empty($data['towers'])) {
                    foreach ($data['towers'] as $towerData) {
                        $project->towers()->create([
                            'name' => $towerData['name'],
                            'total_floors' => $towerData['total_floors'],
                            'units_per_floor' => $towerData['units_per_floor'] ?? null,
                            'basement_levels' => $towerData['basement_levels'] ?? 0,
                            'has_terrace' => isset($towerData['has_terrace']) && $towerData['has_terrace'] == '1',
                            'status' => $towerData['status'],
                            'completion_date' => $towerData['completion_date'] ?? null,
                            'sort_order' => $towerData['sort_order'] ?? 0,
                        ]);
                    }
                }
                unset($data['towers']);

                // Handle project units
                if (!empty($data['units'])) {
                    foreach ($data['units'] as $unitData) {
                        // Handle floor plan image upload
                        $floorPlanPath = null;
                        if (isset($unitData['floor_plan_image']) && $unitData['floor_plan_image']) {
                            $floorPlanPath = $unitData['floor_plan_image']->store('projects/floor-plans', 'public');
                        }
                        
                        $project->units()->create([
                            'name' => $unitData['name'],
                            'type' => $unitData['type'],
                            'carpet_area_sqft' => $unitData['carpet_area_sqft'] ?? null,
                            'built_up_area_sqft' => $unitData['built_up_area_sqft'] ?? null,
                            'super_built_up_sqft' => $unitData['super_built_up_sqft'] ?? null,
                            'bedrooms' => $unitData['bedrooms'] ?? null,
                            'bathrooms' => $unitData['bathrooms'] ?? null,
                            'balconies' => $unitData['balconies'] ?? null,
                            'facing' => $unitData['facing'] ?? null,
                            'floor_plan_image' => $floorPlanPath ? '/storage/' . $floorPlanPath : null,
                            'base_price' => $unitData['base_price'] ?? null,
                            'total_units' => $unitData['total_units'] ?? 1,
                            'available_units' => $unitData['available_units'] ?? 1,
                            'is_active' => isset($unitData['is_active']) && $unitData['is_active'] == '1',
                            'sort_order' => $unitData['sort_order'] ?? 0,
                        ]);
                    }
                }
                unset($data['units']);

                // Handle project specifications (polymorphic)
                if ($project->type === 'residential' && !empty($data['residential_spec'])) {
                    $specData = $data['residential_spec'];
                    $project->residentialSpec()->create([
                        'total_towers' => $specData['total_towers'] ?? null,
                        'total_floors_per_tower' => $specData['total_floors_per_tower'] ?? null,
                        'total_units' => $specData['total_units'] ?? null,
                        'units_per_floor' => $specData['units_per_floor'] ?? null,
                        'basement_levels' => $specData['basement_levels'] ?? 0,
                        'open_parking_slots' => $specData['open_parking_slots'] ?? null,
                        'covered_parking_slots' => $specData['covered_parking_slots'] ?? null,
                        'clubhouse_area_sqft' => $specData['clubhouse_area_sqft'] ?? null,
                        'stilt_parking' => isset($specData['stilt_parking']) && $specData['stilt_parking'] == '1',
                        'podium_level' => isset($specData['podium_level']) && $specData['podium_level'] == '1',
                    ]);
                } elseif ($project->type === 'commercial' && !empty($data['commercial_spec'])) {
                    $specData = $data['commercial_spec'];
                    $project->commercialSpec()->create([
                        'total_floors' => $specData['total_floors'] ?? null,
                        'total_units' => $specData['total_units'] ?? null,
                        'office_units' => $specData['office_units'] ?? null,
                        'retail_units' => $specData['retail_units'] ?? null,
                        'food_court_area_sqft' => $specData['food_court_area_sqft'] ?? null,
                        'common_area_percentage' => $specData['common_area_percentage'] ?? null,
                        'visitor_parking_slots' => $specData['visitor_parking_slots'] ?? null,
                        'tenant_parking_slots' => $specData['tenant_parking_slots'] ?? null,
                        'green_building_certified' => isset($specData['green_building_certified']) && $specData['green_building_certified'] == '1',
                        'certification_type' => $specData['certification_type'] ?? null,
                    ]);
                } elseif ($project->type === 'villa' && !empty($data['villa_spec'])) {
                    $specData = $data['villa_spec'];
                    $project->villaSpec()->create([
                        'total_villas' => $specData['total_villas'] ?? null,
                        'villa_types' => $specData['villa_types'] ?? null,
                        'floors_per_villa' => $specData['floors_per_villa'] ?? null,
                        'car_parking_per_villa' => $specData['car_parking_per_villa'] ?? null,
                        'clubhouse_area_sqft' => $specData['clubhouse_area_sqft'] ?? null,
                        'private_garden' => isset($specData['private_garden']) && $specData['private_garden'] == '1',
                        'private_pool' => isset($specData['private_pool']) && $specData['private_pool'] == '1',
                        'servant_quarters' => isset($specData['servant_quarters']) && $specData['servant_quarters'] == '1',
                        'gated_community' => isset($specData['gated_community']) && $specData['gated_community'] == '1',
                    ]);
                } elseif ($project->type === 'open_plot' && !empty($data['open_plot_spec'])) {
                    $specData = $data['open_plot_spec'];
                    $project->openPlotSpec()->create([
                        'total_plots' => $specData['total_plots'] ?? null,
                        'min_plot_size_sqyds' => $specData['min_plot_size_sqyds'] ?? null,
                        'max_plot_size_sqyds' => $specData['max_plot_size_sqyds'] ?? null,
                        'park_area_sqft' => $specData['park_area_sqft'] ?? null,
                        'community_hall_sqft' => $specData['community_hall_sqft'] ?? null,
                        'underground_drainage' => isset($specData['underground_drainage']) && $specData['underground_drainage'] == '1',
                        'underground_electricity' => isset($specData['underground_electricity']) && $specData['underground_electricity'] == '1',
                        'water_supply' => isset($specData['water_supply']) && $specData['water_supply'] == '1',
                        'compound_wall' => isset($specData['compound_wall']) && $specData['compound_wall'] == '1',
                        'avenue_plantation' => isset($specData['avenue_plantation']) && $specData['avenue_plantation'] == '1',
                        'fencing' => isset($specData['fencing']) && $specData['fencing'] == '1',
                    ]);
                }
                unset($data['residential_spec'], $data['commercial_spec'], $data['villa_spec'], $data['open_plot_spec']);

                return $project;
            });

            return response()->json([
                'message' => 'Project created successfully.',
                'data' => $project->load(['company', 'images', 'amenities', 'towers', 'units', 'residentialSpec', 'commercialSpec', 'villaSpec', 'openPlotSpec']),
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('Error creating project: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create project.',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
            $updatedProject = \DB::transaction(function () use ($request, $project) {
                $data = $request->validated();
                
                // Handle image deletions
                $deleteImages = $data['delete_images'] ?? [];
                unset($data['delete_images']);
                
                if (!empty($deleteImages)) {
                    foreach ($deleteImages as $imageId) {
                        if ($imageId) {
                            $image = $project->images()->find($imageId);
                            if ($image) {
                                // Delete file from storage
                                $imagePath = str_replace('/storage/', '', $image->image_path);
                                \Storage::disk('public')->delete($imagePath);
                                // Delete database record
                                $image->delete();
                            }
                        }
                    }
                }
                
                // Handle new images
                $imagesData = $data['images'] ?? [];
                unset($data['images']);

                // Handle logo upload
                if ($request->hasFile('logo')) {
                    // Delete old logo if exists
                    if ($project->logo) {
                        $oldPath = str_replace('/storage/', '', $project->logo);
                        \Storage::disk('public')->delete($oldPath);
                    }

                    $logoPath = $request->file('logo')->store('projects/logos', 'public');
                    $data['logo'] = '/storage/' . $logoPath;
                }

                // Update project
                $project->update($data);
                
                // Add new images
                if (!empty($imagesData)) {
                    foreach ($imagesData as $imageData) {
                        if (isset($imageData['file'])) {
                            // Upload image
                            $imagePath = $imageData['file']->store('projects/images', 'public');
                            
                            // Create image record
                            $project->images()->create([
                                'image_path' => '/storage/' . $imagePath,
                                'title' => $imageData['title'] ?? null,
                                'alt_text' => $imageData['alt_text'] ?? null,
                                'type' => $imageData['type'],
                                'sort_order' => $imageData['sort_order'] ?? 0,
                                'is_primary' => isset($imageData['is_primary']) && $imageData['is_primary'] == '1',
                            ]);
                        }
                    }
                }

            // Handle amenity deletions
            $deleteAmenities = $data['delete_amenities'] ?? [];
            unset($data['delete_amenities']);
            
            if (!empty($deleteAmenities)) {
                foreach ($deleteAmenities as $amenityId) {
                    if ($amenityId) {
                        $project->amenities()->where('id', $amenityId)->delete();
                    }
                }
            }
            
            // Handle new amenities
            $amenitiesData = $data['amenities'] ?? [];
            unset($data['amenities']);
            
            if (!empty($amenitiesData)) {
                foreach ($amenitiesData as $amenityData) {
                    $project->amenities()->create([
                        'name' => $amenityData['name'],
                        'category' => $amenityData['category'],
                        'icon' => $amenityData['icon'] ?? null,
                        'description' => $amenityData['description'] ?? null,
                        'is_highlighted' => isset($amenityData['is_highlighted']) && $amenityData['is_highlighted'] == '1',
                        'sort_order' => $amenityData['sort_order'] ?? 0,
                    ]);
                }
            }

            // Handle tower deletions
            $deleteTowers = $data['delete_towers'] ?? [];
            unset($data['delete_towers']);
            
            if (!empty($deleteTowers)) {
                foreach ($deleteTowers as $towerId) {
                    if ($towerId) {
                        $project->towers()->where('id', $towerId)->delete();
                    }
                }
            }
            
            // Handle new towers
            $towersData = $data['towers'] ?? [];
            unset($data['towers']);
            
            if (!empty($towersData)) {
                foreach ($towersData as $towerData) {
                    $project->towers()->create([
                        'name' => $towerData['name'],
                        'total_floors' => $towerData['total_floors'],
                        'units_per_floor' => $towerData['units_per_floor'] ?? null,
                        'basement_levels' => $towerData['basement_levels'] ?? 0,
                        'has_terrace' => isset($towerData['has_terrace']) && $towerData['has_terrace'] == '1',
                        'status' => $towerData['status'],
                        'completion_date' => $towerData['completion_date'] ?? null,
                        'sort_order' => $towerData['sort_order'] ?? 0,
                    ]);
                }
            }

            // Handle unit deletions
            $deleteUnits = $data['delete_units'] ?? [];
            unset($data['delete_units']);
            
            if (!empty($deleteUnits)) {
                foreach ($deleteUnits as $unitId) {
                    if ($unitId) {
                        $unit = $project->units()->find($unitId);
                        if ($unit) {
                            // Delete floor plan image from storage
                            if ($unit->floor_plan_image) {
                                $imagePath = str_replace('/storage/', '', $unit->floor_plan_image);
                                \Storage::disk('public')->delete($imagePath);
                            }
                            $unit->delete();
                        }
                    }
                }
            }
            
            // Handle new units
            $unitsData = $data['units'] ?? [];
            unset($data['units']);
            
            if (!empty($unitsData)) {
                foreach ($unitsData as $unitData) {
                    // Handle floor plan image upload
                    $floorPlanPath = null;
                    if (isset($unitData['floor_plan_image']) && $unitData['floor_plan_image']) {
                        $floorPlanPath = $unitData['floor_plan_image']->store('projects/floor-plans', 'public');
                    }
                    
                    $project->units()->create([
                        'name' => $unitData['name'],
                        'type' => $unitData['type'],
                        'carpet_area_sqft' => $unitData['carpet_area_sqft'] ?? null,
                        'built_up_area_sqft' => $unitData['built_up_area_sqft'] ?? null,
                        'super_built_up_sqft' => $unitData['super_built_up_sqft'] ?? null,
                        'bedrooms' => $unitData['bedrooms'] ?? null,
                        'bathrooms' => $unitData['bathrooms'] ?? null,
                        'balconies' => $unitData['balconies'] ?? null,
                        'facing' => $unitData['facing'] ?? null,
                        'floor_plan_image' => $floorPlanPath ? '/storage/' . $floorPlanPath : null,
                        'base_price' => $unitData['base_price'] ?? null,
                        'total_units' => $unitData['total_units'] ?? 1,
                        'available_units' => $unitData['available_units'] ?? 1,
                        'is_active' => isset($unitData['is_active']) && $unitData['is_active'] == '1',
                        'sort_order' => $unitData['sort_order'] ?? 0,
                    ]);
                }
            }

            // Handle project specifications (polymorphic - updateOrCreate)
            if ($project->type === 'residential' && !empty($data['residential_spec'])) {
                $specData = $data['residential_spec'];
                $project->residentialSpec()->updateOrCreate(['project_id' => $project->id], [
                    'total_towers' => $specData['total_towers'] ?? null,
                    'total_floors_per_tower' => $specData['total_floors_per_tower'] ?? null,
                    'total_units' => $specData['total_units'] ?? null,
                    'units_per_floor' => $specData['units_per_floor'] ?? null,
                    'basement_levels' => $specData['basement_levels'] ?? 0,
                    'open_parking_slots' => $specData['open_parking_slots'] ?? null,
                    'covered_parking_slots' => $specData['covered_parking_slots'] ?? null,
                    'clubhouse_area_sqft' => $specData['clubhouse_area_sqft'] ?? null,
                    'stilt_parking' => isset($specData['stilt_parking']) && $specData['stilt_parking'] == '1',
                    'podium_level' => isset($specData['podium_level']) && $specData['podium_level'] == '1',
                ]);
            } elseif ($project->type === 'commercial' && !empty($data['commercial_spec'])) {
                $specData = $data['commercial_spec'];
                $project->commercialSpec()->updateOrCreate(['project_id' => $project->id], [
                    'total_floors' => $specData['total_floors'] ?? null,
                    'total_units' => $specData['total_units'] ?? null,
                    'office_units' => $specData['office_units'] ?? null,
                    'retail_units' => $specData['retail_units'] ?? null,
                    'food_court_area_sqft' => $specData['food_court_area_sqft'] ?? null,
                    'common_area_percentage' => $specData['common_area_percentage'] ?? null,
                    'visitor_parking_slots' => $specData['visitor_parking_slots'] ?? null,
                    'tenant_parking_slots' => $specData['tenant_parking_slots'] ?? null,
                    'green_building_certified' => isset($specData['green_building_certified']) && $specData['green_building_certified'] == '1',
                    'certification_type' => $specData['certification_type'] ?? null,
                ]);
            } elseif ($project->type === 'villa' && !empty($data['villa_spec'])) {
                $specData = $data['villa_spec'];
                $project->villaSpec()->updateOrCreate(['project_id' => $project->id], [
                    'total_villas' => $specData['total_villas'] ?? null,
                    'villa_types' => $specData['villa_types'] ?? null,
                    'floors_per_villa' => $specData['floors_per_villa'] ?? null,
                    'car_parking_per_villa' => $specData['car_parking_per_villa'] ?? null,
                    'clubhouse_area_sqft' => $specData['clubhouse_area_sqft'] ?? null,
                    'private_garden' => isset($specData['private_garden']) && $specData['private_garden'] == '1',
                    'private_pool' => isset($specData['private_pool']) && $specData['private_pool'] == '1',
                    'servant_quarters' => isset($specData['servant_quarters']) && $specData['servant_quarters'] == '1',
                    'gated_community' => isset($specData['gated_community']) && $specData['gated_community'] == '1',
                ]);
            } elseif ($project->type === 'open_plot' && !empty($data['open_plot_spec'])) {
                $specData = $data['open_plot_spec'];
                $project->openPlotSpec()->updateOrCreate(['project_id' => $project->id], [
                    'total_plots' => $specData['total_plots'] ?? null,
                    'min_plot_size_sqyds' => $specData['min_plot_size_sqyds'] ?? null,
                    'max_plot_size_sqyds' => $specData['max_plot_size_sqyds'] ?? null,
                    'park_area_sqft' => $specData['park_area_sqft'] ?? null,
                    'community_hall_sqft' => $specData['community_hall_sqft'] ?? null,
                    'underground_drainage' => isset($specData['underground_drainage']) && $specData['underground_drainage'] == '1',
                    'underground_electricity' => isset($specData['underground_electricity']) && $specData['underground_electricity'] == '1',
                    'water_supply' => isset($specData['water_supply']) && $specData['water_supply'] == '1',
                    'compound_wall' => isset($specData['compound_wall']) && $specData['compound_wall'] == '1',
                    'avenue_plantation' => isset($specData['avenue_plantation']) && $specData['avenue_plantation'] == '1',
                    'fencing' => isset($specData['fencing']) && $specData['fencing'] == '1',
                ]);
            }
            unset($data['residential_spec'], $data['commercial_spec'], $data['villa_spec'], $data['open_plot_spec']);

            // Refresh to get latest data
            return $project->fresh()->load(['company', 'images', 'amenities', 'towers', 'units', 'residentialSpec', 'commercialSpec', 'villaSpec', 'openPlotSpec']);
        });

            return response()->json([
                'message' => 'Project updated successfully.',
                'data' => $updatedProject,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating project: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update project.',
                'error' => $e->getMessage()
            ], 500);
        }
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
