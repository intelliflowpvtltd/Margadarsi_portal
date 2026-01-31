<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AmenityController extends BaseMasterController
{
    protected string $modelClass = Amenity::class;
    protected string $viewPath = 'masters.amenities';

    protected array $validationRules = [
        'category_id' => 'required|exists:amenity_categories,id',
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:50',
        'description' => 'nullable|string',
        'is_premium' => 'nullable|boolean',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('category_id') && $request->category_id) {
            $query->inCategory($request->category_id);
        }

        if ($request->has('is_premium') && $request->is_premium !== null) {
            if ($request->boolean('is_premium')) {
                $query->premium();
            } else {
                $query->standard();
            }
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->load('category');
    }

    /**
     * Get amenities by category
     */
    public function byCategory(int $categoryId): JsonResponse
    {
        $amenities = Amenity::where('category_id', $categoryId)
                           ->active()
                           ->ordered()
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $amenities
        ]);
    }
}
