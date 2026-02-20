<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetRange;
use App\Models\ClosureReason;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\LeadStatus;
use App\Models\NqReason;
use App\Models\PropertyType;
use App\Models\SourceCategory;
use App\Models\TemperatureTag;
use App\Models\Timeline;
use Illuminate\Http\JsonResponse;

/**
 * Lightweight read-only endpoints for populating form dropdowns.
 * Returns id + name for active records, sorted by sort_order.
 */
class LookupController extends Controller
{
    public function leadSources(): JsonResponse
    {
        $items = LeadSource::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'slug')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function budgetRanges(): JsonResponse
    {
        $items = BudgetRange::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'min_amount', 'max_amount')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function propertyTypes(): JsonResponse
    {
        $items = PropertyType::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'slug', 'icon', 'color_code')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function timelines(): JsonResponse
    {
        $items = Timeline::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function temperatureTags(): JsonResponse
    {
        $items = TemperatureTag::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'color_code', 'sla_minutes')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function closureReasons(): JsonResponse
    {
        $items = ClosureReason::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function nqReasons(): JsonResponse
    {
        $items = NqReason::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function sourceCategories(): JsonResponse
    {
        $items = SourceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'slug')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function leadStatuses(): JsonResponse
    {
        $items = LeadStatus::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'slug', 'color_code')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function leadStages(): JsonResponse
    {
        $items = LeadStage::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'slug', 'phase')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }
}
