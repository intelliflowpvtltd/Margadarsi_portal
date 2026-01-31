<?php

namespace App\Http\Controllers;

use App\Models\GenericMaster;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GenericMasterController extends BaseMasterController
{
    protected string $modelClass = GenericMaster::class;
    protected string $viewPath = 'masters.generic';

    protected array $validationRules = [
        'type' => 'required|string|max:50',
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'value' => 'nullable|string|max:255',
        'metadata' => 'nullable|array',
        'parent_id' => 'nullable|exists:masters,id',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('type') && $request->type) {
            $query->ofType($request->type);
        }

        if ($request->has('parent_id')) {
            if ($request->parent_id) {
                $query->where('parent_id', $request->parent_id);
            } else {
                $query->rootLevel();
            }
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->load('parent', 'children');
    }

    /**
     * Get masters by type
     */
    public function byType(string $type): JsonResponse
    {
        $items = GenericMaster::where('type', $type)
                             ->active()
                             ->ordered()
                             ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Get available master types
     */
    public function types(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => GenericMaster::getAvailableTypes()
        ]);
    }
}
