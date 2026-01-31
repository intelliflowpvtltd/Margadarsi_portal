<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CityController extends BaseMasterController
{
    protected string $modelClass = City::class;
    protected string $viewPath = 'masters.cities';

    protected array $validationRules = [
        'state_id' => 'required|exists:states,id',
        'name' => 'required|string|max:100',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'is_metro' => 'nullable|boolean',
        'is_tier1' => 'nullable|boolean',
        'is_tier2' => 'nullable|boolean',
        'is_tier3' => 'nullable|boolean',
        'pincode_prefix' => 'nullable|string|max:10',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ];

    protected function applyFilters($query, Request $request)
    {
        if ($request->has('state_id') && $request->state_id) {
            $query->inState($request->state_id);
        }

        if ($request->has('is_metro') && $request->is_metro !== null) {
            $query->where('is_metro', $request->boolean('is_metro'));
        }

        if ($request->has('tier')) {
            switch ($request->tier) {
                case '1':
                case 'tier1':
                    $query->tier1();
                    break;
                case '2':
                case 'tier2':
                    $query->tier2();
                    break;
                case '3':
                case 'tier3':
                    $query->tier3();
                    break;
            }
        }

        return $query;
    }

    protected function loadRelationships($item)
    {
        return $item->load('state.country');
    }

    /**
     * Get cities by state
     */
    public function byState(int $stateId): JsonResponse
    {
        $cities = City::where('state_id', $stateId)
                     ->active()
                     ->ordered()
                     ->get();

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    /**
     * Get metro cities
     */
    public function metroOnly(): JsonResponse
    {
        $cities = City::metro()
                     ->active()
                     ->ordered()
                     ->with('state.country')
                     ->get();

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
}
