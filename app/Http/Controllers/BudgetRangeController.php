<?php

namespace App\Http\Controllers;

use App\Models\BudgetRange;

class BudgetRangeController extends BaseMasterController
{
    protected string $modelClass = BudgetRange::class;
    protected string $viewPath = 'masters.budget-ranges';

    protected array $validationRules = [
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'min_amount' => 'required|numeric|min:0',
        'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function loadRelationships($item)
    {
        return $item->loadCount('leads');
    }
}
