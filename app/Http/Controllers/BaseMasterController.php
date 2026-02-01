<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;

abstract class BaseMasterController extends Controller
{
    /**
     * Model class to be used by this controller
     * Must be set in child classes
     */
    protected string $modelClass;

    /**
     * View path for Blade views (optional)
     */
    protected string $viewPath;

    /**
     * Validation rules for store
     */
    protected array $validationRules = [];

    /**
     * Validation rules for update (uses store rules if not set)
     */
    protected array $updateValidationRules = [];

    /**
     * Display a listing of the resource
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->modelClass::query();

        // Apply search if provided
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Apply active filter
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Apply any custom filters from child controller
        $query = $this->applyFilters($query, $request);

        // Determine if we should paginate
        $perPage = $request->input('per_page', 100);
        
        if ($request->boolean('paginate', false)) {
            $items = $query->active()->ordered()->paginate($perPage);
        } else {
            $items = $query->active()->ordered()->get();
        }

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules);
        
        // Auto-inject company_id if the model has it in fillable and user is authenticated
        $model = new $this->modelClass;
        if (in_array('company_id', $model->getFillable()) && auth()->check()) {
            $validated['company_id'] = auth()->user()->company_id;
        }
        
        $item = $this->modelClass::create($validated);

        // Load any relationships
        if (method_exists($this, 'loadRelationships')) {
            $item = $this->loadRelationships($item);
        }

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Display the specified resource
     */
    public function show(int $id): JsonResponse
    {
        $item = $this->modelClass::findOrFail($id);

        // Load any relationships
        if (method_exists($this, 'loadRelationships')) {
            $item = $this->loadRelationships($item);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $item = $this->modelClass::findOrFail($id);
        
        $rules = !empty($this->updateValidationRules) 
            ? $this->updateValidationRules 
            : $this->validationRules;
        
        $validated = $request->validate($rules);
        $item->update($validated);

        // Load any relationships
        if (method_exists($this, 'loadRelationships')) {
            $item = $this->loadRelationships($item);
        }

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource
     */
    public function destroy(int $id): JsonResponse
    {
        $item = $this->modelClass::findOrFail($id);

        if (!$item->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete: This item is currently in use'
            ], 422);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): JsonResponse
    {
        $item = $this->modelClass::findOrFail($id);
        $item->toggleActive();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $item
        ]);
    }

    /**
     * Apply custom filters (override in child controllers)
     */
    protected function applyFilters($query, Request $request)
    {
        return $query;
    }

    /**
     * Load relationships (override in child controllers)
     */
    protected function loadRelationships($item)
    {
        return $item;
    }
}
