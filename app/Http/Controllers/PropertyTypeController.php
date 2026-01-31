<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\JsonResponse;

class PropertyTypeController extends BaseMasterController
{
    protected string $modelClass = PropertyType::class;
    protected string $viewPath = 'masters.property-types';

    protected array $validationRules = [
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:50',
        'color_code' => 'nullable|string|max:7',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function loadRelationships($item)
    {
        return $item->loadCount('propertyStatuses');
    }

    /**
     * Get property statuses for a property type
     */
    public function statuses(int $propertyTypeId): JsonResponse
    {
        $propertyType = PropertyType::findOrFail($propertyTypeId);
        
        $statuses = $propertyType->propertyStatuses()
                                 ->active()
                                 ->workflowOrder()
                                 ->get();

        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
    }
}
