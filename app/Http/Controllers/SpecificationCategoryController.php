<?php

namespace App\Http\Controllers;

use App\Models\SpecificationCategory;
use Illuminate\Http\Request;

class SpecificationCategoryController extends BaseMasterController
{
    protected string $modelClass = SpecificationCategory::class;
    protected string $viewPath = 'masters.specification-categories';

    protected array $validationRules = [
        'property_type_id' => 'nullable|exists:property_types,id',
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:50',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('property_type_id')) {
            $query->forPropertyType($request->property_type_id);
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->load('propertyType')->loadCount('specificationTypes');
    }
}
