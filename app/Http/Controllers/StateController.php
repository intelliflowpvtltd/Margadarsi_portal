<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StateController extends BaseMasterController
{
    protected string $modelClass = State::class;
    protected string $viewPath = 'masters.states';

    protected array $validationRules = [
        'country_id' => 'required|exists:countries,id',
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:10',
        'slug' => 'nullable|string|max:100',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('country_id') && $request->country_id) {
            $query->inCountry($request->country_id);
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->load('country', 'cities');
    }

    /**
     * Get states by country
     */
    public function byCountry(int $countryId): JsonResponse
    {
        $states = State::where('country_id', $countryId)
                      ->active()
                      ->ordered()
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $states
        ]);
    }
}
