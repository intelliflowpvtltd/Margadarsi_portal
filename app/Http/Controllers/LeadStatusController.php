<?php

namespace App\Http\Controllers;

use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadStatusController extends BaseMasterController
{
    protected string $modelClass = LeadStatus::class;
    protected string $viewPath = 'masters.lead-statuses';

    protected array $validationRules = [
        'name' => 'required|string|max:100',
        'slug' => 'nullable|string|max:100',
        'color_code' => 'nullable|string|max:7',
        'badge_class' => 'nullable|string|max:50',
        'workflow_order' => 'nullable|integer',
        'is_pipeline_state' => 'nullable|boolean',
        'is_final_state' => 'nullable|boolean',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('is_pipeline_state') && $request->is_pipeline_state !== null) {
            $query->where('is_pipeline_state', $request->boolean('is_pipeline_state'));
        }

        if ($request->has('is_final_state') && $request->is_final_state !== null) {
            $query->where('is_final_state', $request->boolean('is_final_state'));
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->loadCount('leads');
    }

    /**
     * Get pipeline statuses only
     */
    public function pipeline(): JsonResponse
    {
        $statuses = LeadStatus::where('is_pipeline_state', true)
                             ->active()
                             ->orderBy('workflow_order')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
    }

    /**
     * Get final statuses only
     */
    public function final(): JsonResponse
    {
        $statuses = LeadStatus::where('is_final_state', true)
                             ->active()
                             ->ordered()
                             ->get();

        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
    }
}
