<?php

use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// Company Routes
Route::prefix('v1')->group(function () {
    // Company CRUD
    Route::apiResource('companies', CompanyController::class);

    // Restore soft-deleted company
    Route::post('companies/{company}/restore', [CompanyController::class, 'restore'])
        ->name('companies.restore')
        ->withTrashed();

    // Permanently delete company
    Route::delete('companies/{company}/force', [CompanyController::class, 'forceDelete'])
        ->name('companies.force-delete')
        ->withTrashed();
});
