<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies (web route).
     */
    public function index(Request $request)
    {
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getCompaniesJson($request);
        }

        // Otherwise return view
        return view('companies.index');
    }

    /**
     * Get companies as JSON for AJAX requests.
     */
    private function getCompaniesJson(Request $request): JsonResponse
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

        return response()->json([
            'data' => $companies->items(),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'from' => $companies->firstItem(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'to' => $companies->lastItem(),
                'total' => $companies->total(),
            ]
        ]);
    }

    /**
     * Store a newly created company.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $data['logo'] = '/storage/' . $logoPath;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('companies/favicons', 'public');
            $data['favicon'] = '/storage/' . $faviconPath;
        }

        $company = Company::create($data);

        return response()->json([
            'message' => 'Company created successfully.',
            'data' => $company,
        ], 201);
    }

    /**
     * Display the specified company.
     */
    public function show(Request $request, Company $company)
    {
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $company,
            ]);
        }

        // Load relationship counts for the view
        $company->loadCount(['projects', 'users', 'roles']);

        // Otherwise return view
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified company.
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo) {
                $oldPath = str_replace('/storage/', '', $company->logo);
                Storage::disk('public')->delete($oldPath);
            }

            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $data['logo'] = '/storage/' . $logoPath;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($company->favicon) {
                $oldPath = str_replace('/storage/', '', $company->favicon);
                Storage::disk('public')->delete($oldPath);
            }

            $faviconPath = $request->file('favicon')->store('companies/favicons', 'public');
            $data['favicon'] = '/storage/' . $faviconPath;
        }

        $company->update($data);

        // Refresh to get latest data with relationships
        $company->fresh();

        return response()->json([
            'message' => 'Company updated successfully.',
            'data' => $company,
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
            'data' => $company,
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
