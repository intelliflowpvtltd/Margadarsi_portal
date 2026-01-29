<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\PasswordResetController;
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
    // ==================== PUBLIC AUTH ROUTES ====================
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('auth/verify-otp', [PasswordResetController::class, 'verifyOtp']);
    Route::post('auth/reset-password', [PasswordResetController::class, 'resetPassword']);

    // ==================== PROTECTED AUTH ROUTES ====================
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // ==================== COMPANY ROUTES ====================
        Route::get('companies', [CompanyController::class, 'index'])
            ->middleware('permission:companies.view');
        Route::post('companies', [CompanyController::class, 'store'])
            ->middleware('permission:companies.create');
        Route::get('companies/{company}', [CompanyController::class, 'show'])
            ->middleware('permission:companies.view');
        Route::put('companies/{company}', [CompanyController::class, 'update'])
            ->middleware('permission:companies.update');
        Route::delete('companies/{company}', [CompanyController::class, 'destroy'])
            ->middleware('permission:companies.delete');
        Route::post('companies/{company}/restore', [CompanyController::class, 'restore'])
            ->middleware('permission:companies.restore')
            ->withTrashed();
        Route::delete('companies/{company}/force', [CompanyController::class, 'forceDelete'])
            ->middleware('permission:companies.force-delete')
            ->withTrashed();

        // ==================== PROJECT ROUTES ====================
        Route::get('projects', [ProjectController::class, 'index'])
            ->middleware('permission:projects.view');
        Route::post('projects', [ProjectController::class, 'store'])
            ->middleware('permission:projects.create');
        Route::get('projects/{project}', [ProjectController::class, 'show'])
            ->middleware('permission:projects.view');
        Route::put('projects/{project}', [ProjectController::class, 'update'])
            ->middleware('permission:projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])
            ->middleware('permission:projects.delete');
        Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])
            ->middleware('permission:projects.restore')
            ->withTrashed();
        Route::delete('projects/{project}/force', [ProjectController::class, 'forceDelete'])
            ->middleware('permission:projects.force-delete')
            ->withTrashed();
        Route::put('projects/{project}/specification', [ProjectController::class, 'updateSpecification'])
            ->middleware('permission:projects.manage-specifications');

        // ==================== ROLE ROUTES ====================
        Route::get('roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view');
        Route::post('roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create');
        Route::get('roles/{role}', [RoleController::class, 'show'])
            ->middleware('permission:roles.view');
        Route::put('roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete');
        Route::post('roles/{role}/restore', [RoleController::class, 'restore'])
            ->middleware('permission:roles.restore')
            ->withTrashed();
        Route::get('roles-config/system', [RoleController::class, 'systemRoles'])
            ->middleware('permission:roles.view');
        Route::post('roles-config/seed', [RoleController::class, 'seedSystemRoles'])
            ->middleware('permission:roles.seed');

        // ==================== USER ROUTES ====================
        Route::get('users', [UserController::class, 'index'])
            ->middleware('permission:users.view');
        Route::post('users', [UserController::class, 'store'])
            ->middleware('permission:users.create');
        Route::get('users/{user}', [UserController::class, 'show'])
            ->middleware('permission:users.view');
        Route::put('users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete');
        Route::post('users/{user}/restore', [UserController::class, 'restore'])
            ->middleware('permission:users.restore')
            ->withTrashed();
        Route::get('users/{user}/projects', [UserController::class, 'projects'])
            ->middleware('permission:users.view');
        Route::post('users/{user}/projects', [UserController::class, 'assignProjects'])
            ->middleware('permission:users.assign-projects');
        Route::delete('users/{user}/projects/{project}', [UserController::class, 'removeProject'])
            ->middleware('permission:users.assign-projects');
    });
});
