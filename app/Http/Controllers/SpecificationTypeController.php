<?php

namespace App\Http\Controllers;

use App\Models\SpecificationType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SpecificationTypeController extends BaseMasterController
{
    protected string $modelClass = SpecificationType::class;
    protected string $viewPath = 'masters.specification-types';

    protected array $validationRules = [
        'category_id' => 'required|exists:specification_categories,id',
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'data_type' => 'required|in:text,number,boolean,select,date,textarea',
        'allowed_values' => 'nullable|array',
        'unit' => 'nullable|string|max:50',
        'is_required' => 'nullable|boolean',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('category_id') && $request->category_id) {
            $query->inCategory($request->category_id);
        }

        if ($request->has('data_type') && $request->data_type) {
            $query->byDataType($request->data_type);
        }

        if ($request->has('is_required') && $request->is_required !== null) {
            if ($request->boolean('is_required')) {
                $query->required();
            } else {
                $query->optional();
            }
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->load('category');
    }

    /**
     * Get specification types by category
     */
    public function byCategory(int $categoryId): JsonResponse
    {
        $types = SpecificationType::where('category_id', $categoryId)
                                  ->active()
                                  ->ordered()
                                  ->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }
}
