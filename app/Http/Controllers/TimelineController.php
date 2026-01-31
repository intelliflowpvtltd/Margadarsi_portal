<?php

namespace App\Http\Controllers;

use App\Models\Timeline;

class TimelineController extends BaseMasterController
{
    protected string $modelClass = Timeline::class;
    protected string $viewPath = 'masters.timelines';

    protected array $validationRules = [
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'days' => 'nullable|integer|min:0',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function loadRelationships($item)
    {
        return $item->loadCount('leads');
    }
}
