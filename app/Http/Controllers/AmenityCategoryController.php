<?php

namespace App\Http\Controllers;

use App\Models\AmenityCategory;

class AmenityCategoryController extends BaseMasterController
{
    protected string $modelClass = AmenityCategory::class;
    protected string $viewPath = 'masters.amenity-categories';

    protected array $validationRules = [
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:50',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function loadRelationships($item)
    {
        return $item->loadCount('amenities');
    }
}
