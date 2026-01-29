<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
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

    Route::get('roles-config/system', [RoleController::class, 'systemRoles'])
        ->name('roles.system-config');

    Route::post('roles-config/seed', [RoleController::class, 'seedSystemRoles'])
        ->name('roles.seed');

    // ==================== USER ROUTES ====================
    Route::apiResource('users', UserController::class);

    Route::post('users/{user}/restore', [UserController::class, 'restore'])
        ->name('users.restore')
        ->withTrashed();

    // Project assignment routes
    Route::get('users/{user}/projects', [UserController::class, 'projects'])
        ->name('users.projects');

    Route::post('users/{user}/projects', [UserController::class, 'assignProjects'])
        ->name('users.assign-projects');

    Route::delete('users/{user}/projects/{project}', [UserController::class, 'removeProject'])
        ->name('users.remove-project');
});
