<?php

namespace App\Http\Controllers;

use App\Models\PropertyStatus;
use Illuminate\Http\Request;

class PropertyStatusController extends BaseMasterController
{
    protected string $modelClass = PropertyStatus::class;
    protected string $viewPath = 'masters.property-statuses';

    protected array $validationRules = [
        'property_type_id' => 'nullable|exists:property_types,id',
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'color_code' => 'nullable|string|max:7',
        'badge_class' => 'nullable|string|max:50',
        'workflow_order' => 'nullable|integer',
        'is_final_state' => 'nullable|boolean',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('property_type_id') && $request->property_type_id) {
            $query->forPropertyType($request->property_type_id);
        }

        if ($request->has('is_final_state') && $request->is_final_state !== null) {
            if ($request->boolean('is_final_state')) {
                $query->final();
            } else {
                $query->pipeline();
            }
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->load('propertyType');
    }
}
