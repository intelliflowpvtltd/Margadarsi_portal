<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DepartmentController;
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
    // Rate limiting to prevent brute force attacks
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
    Route::post('auth/forgot-password', [PasswordResetController::class, 'forgotPassword'])
        ->middleware('throttle:3,1'); // 3 attempts per minute
    Route::post('auth/verify-otp', [PasswordResetController::class, 'verifyOtp'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
    Route::post('auth/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->middleware('throttle:3,1'); // 3 attempts per minute

    // ==================== PROTECTED AUTH ROUTES ====================
    // Support both Sanctum tokens (for external APIs) and web sessions (for Blade frontend)
    Route::middleware('auth:sanctum,web')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
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

        // ==================== DEPARTMENT ROUTES ====================
        Route::get('departments', [DepartmentController::class, 'index'])
            ->middleware('permission:departments.view');
        Route::post('departments', [DepartmentController::class, 'store'])
            ->middleware('permission:departments.create');
        Route::get('departments/{department}', [DepartmentController::class, 'show'])
            ->middleware('permission:departments.view');
        Route::put('departments/{department}', [DepartmentController::class, 'update'])
            ->middleware('permission:departments.update');
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])
            ->middleware('permission:departments.delete');
        Route::get('departments/{department}/stats', [DepartmentController::class, 'stats'])
            ->middleware('permission:departments.stats');

        // ==================== ROLE ROUTES ====================
        Route::get('roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view');
        Route::get('roles/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create');
        Route::post('roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create');
        Route::get('roles/{role}', [RoleController::class, 'show'])
            ->middleware('permission:roles.view')
            ->whereNumber('role');
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

        // ==================== LEAD ROUTES ====================
        Route::get('leads', [\App\Http\Controllers\Api\LeadController::class, 'index'])
            ->middleware('permission:leads.view');
        Route::post('leads', [\App\Http\Controllers\Api\LeadController::class, 'store'])
            ->middleware('permission:leads.create');
        Route::get('leads/statistics', [\App\Http\Controllers\Api\LeadController::class, 'statistics'])
            ->middleware('permission:leads.view');
        Route::get('leads/{lead}', [\App\Http\Controllers\Api\LeadController::class, 'show'])
            ->middleware('permission:leads.view');
        Route::put('leads/{lead}', [\App\Http\Controllers\Api\LeadController::class, 'update'])
            ->middleware('permission:leads.update');
        Route::delete('leads/{lead}', [\App\Http\Controllers\Api\LeadController::class, 'destroy'])
            ->middleware('permission:leads.delete');

        // Lead Workflow Actions
        Route::post('leads/{lead}/call', [\App\Http\Controllers\Api\LeadController::class, 'logCall'])
            ->middleware('permission:leads.log-call');
        Route::post('leads/{lead}/transition', [\App\Http\Controllers\Api\LeadController::class, 'transitionStatus'])
            ->middleware('permission:leads.update');
        Route::post('leads/{lead}/qualify', [\App\Http\Controllers\Api\LeadController::class, 'markQualified'])
            ->middleware('permission:leads.qualify');
        Route::post('leads/{lead}/disqualify', [\App\Http\Controllers\Api\LeadController::class, 'markNotQualified'])
            ->middleware('permission:leads.disqualify');
        Route::post('leads/{lead}/handover', [\App\Http\Controllers\Api\LeadController::class, 'handOver'])
            ->middleware('permission:leads.handover');
        Route::post('leads/{lead}/mark-lost', [\App\Http\Controllers\Api\LeadController::class, 'markLost'])
            ->middleware('permission:leads.update');
        Route::post('leads/{lead}/followup', [\App\Http\Controllers\Api\LeadController::class, 'scheduleFollowup'])
            ->middleware('permission:leads.update');
    });
});
