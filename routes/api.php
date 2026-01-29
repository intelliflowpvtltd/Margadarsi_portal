<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RoleController;
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

Route::prefix('v1')->group(function () {
    // ==================== COMPANY ROUTES ====================
    Route::apiResource('companies', CompanyController::class);

    Route::post('companies/{company}/restore', [CompanyController::class, 'restore'])
        ->name('companies.restore')
        ->withTrashed();

    Route::delete('companies/{company}/force', [CompanyController::class, 'forceDelete'])
        ->name('companies.force-delete')
        ->withTrashed();

    // ==================== PROJECT ROUTES ====================
    Route::apiResource('projects', ProjectController::class);

    Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])
        ->name('projects.restore')
        ->withTrashed();

    Route::delete('projects/{project}/force', [ProjectController::class, 'forceDelete'])
        ->name('projects.force-delete')
        ->withTrashed();

    Route::put('projects/{project}/specification', [ProjectController::class, 'updateSpecification'])
        ->name('projects.specification');

    // ==================== ROLE ROUTES ====================
    Route::apiResource('roles', RoleController::class);

    Route::post('roles/{role}/restore', [RoleController::class, 'restore'])
        ->name('roles.restore')
        ->withTrashed();

    // Get default system roles configuration
    Route::get('roles-config/system', [RoleController::class, 'systemRoles'])
        ->name('roles.system-config');

    // Seed system roles for a company
    Route::post('roles-config/seed', [RoleController::class, 'seedSystemRoles'])
        ->name('roles.seed');
});
