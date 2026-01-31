<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;

class LeadSourceController extends BaseMasterController
{
    protected string $modelClass = LeadSource::class;
    protected string $viewPath = 'masters.lead-sources';

    protected array $validationRules = [
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'icon' => 'nullable|string|max:50',
        'color_code' => 'nullable|string|max:7',
        'category' => 'nullable|string|max:50',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->loadCount('leads');
    }
}
