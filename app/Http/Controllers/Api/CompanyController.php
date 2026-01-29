<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Company::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('legal_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Include soft deleted if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['name', 'legal_name', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $companies = $query->paginate($perPage);

        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created company.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());

        return response()->json([
            'message' => 'Company created successfully.',
            'data' => new CompanyResource($company),
        ], 201);
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company);
    }

    /**
     * Update the specified company.
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());

        return response()->json([
            'message' => 'Company updated successfully.',
            'data' => new CompanyResource($company),
        ]);
    }

    /**
     * Remove the specified company (soft delete).
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json([
            'message' => 'Company deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted company.
     */
    public function restore(int $id): JsonResponse
    {
        $company = Company::withTrashed()->findOrFail($id);
        $company->restore();

        return response()->json([
            'message' => 'Company restored successfully.',
            'data' => new CompanyResource($company),
        ]);
    }

    /**
     * Permanently delete a company.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $company = Company::withTrashed()->findOrFail($id);
        $company->forceDelete();

        return response()->json([
            'message' => 'Company permanently deleted.',
        ]);
    }
}
