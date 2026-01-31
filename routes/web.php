<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest Routes (Not authenticated)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOTP'])->name('password.email');

    // Verify OTP
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOTPForm'])->name('password.verify-otp-form');
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('password.verify-otp');

    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==================== COMPANIES ====================
    Route::middleware('permission:companies.view')->group(function () {
        Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    });
    Route::middleware('permission:companies.create')->group(function () {
        Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
    });
    Route::middleware('permission:companies.update')->group(function () {
        Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::patch('companies/{company}', [CompanyController::class, 'update']);
    });
    Route::middleware('permission:companies.delete')->delete('companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    Route::middleware('permission:companies.restore')->post('companies/{id}/restore', [CompanyController::class, 'restore'])->name('companies.restore');
    Route::middleware('permission:companies.force-delete')->delete('companies/{id}/force', [CompanyController::class, 'forceDelete'])->name('companies.force-delete');

    // ==================== PROJECTS ====================
    // Note: 'create' route must come before '{project}' wildcard route
    Route::middleware('permission:projects.create')->group(function () {
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    });
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    });
    Route::middleware('permission:projects.update')->group(function () {
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::patch('projects/{project}', [ProjectController::class, 'update']);
    });
    Route::middleware('permission:projects.delete')->delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::middleware('permission:projects.restore')->post('projects/{id}/restore', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::middleware('permission:projects.force-delete')->delete('projects/{id}/force', [ProjectController::class, 'forceDelete'])->name('projects.force-delete');

    // ==================== ROLES ====================
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('roles/{id}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions');
    });
    Route::middleware('permission:roles.create')->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware('permission:roles.update')->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::patch('roles/{role}', [RoleController::class, 'update']);
        Route::put('roles/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    });
    Route::middleware('permission:roles.delete')->delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::middleware('permission:roles.restore')->post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
    Route::middleware('permission:roles.force-delete')->delete('roles/{id}/force', [RoleController::class, 'forceDelete'])->name('roles.force-delete');

    // ====================DEPARTMENTS ====================
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('departments', [App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'show'])->name('departments.show');
        Route::get('departments/{department}/stats', [App\Http\Controllers\DepartmentController::class, 'stats'])->name('departments.stats');
    });
    Route::middleware('permission:projects.create')->group(function () {
        Route::post('departments', [App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
    });
    Route::middleware('permission:projects.update')->group(function () {
        Route::put('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'update'])->name('departments.update');
        Route::patch('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'update']);
    });
    Route::middleware('permission:projects.delete')->delete('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.destroy');

    // ==================== USERS ====================
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('permission:users.create')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:users.update')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}', [UserController::class, 'update']);
    });
    Route::middleware('permission:users.delete')->delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::middleware('permission:users.restore')->post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::middleware('permission:users.force-delete')->delete('users/{id}/force', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::middleware('permission:users.assign-projects')->post('users/{user}/projects', [UserController::class, 'assignProjects'])->name('users.assign-projects');


    // ==================== LEADS (Placeholder) ====================
    Route::get('/leads', function () {
        return view('leads.index');
    })->name('leads.index');

    // ==================== MASTER DATA MANAGEMENT ====================
    
    // Admin Routes (Protected by permissions)
    Route::prefix('admin/masters')->middleware('permission:masters.manage')->group(function () {
        
        // Location Masters
        Route::apiResource('countries', App\Http\Controllers\CountryController::class);
        Route::apiResource('states', App\Http\Controllers\StateController::class);
        Route::get('states/by-country/{countryId}', [App\Http\Controllers\StateController::class, 'byCountry'])->name('masters.states.by-country');
        Route::apiResource('cities', App\Http\Controllers\CityController::class);
        Route::get('cities/by-state/{stateId}', [App\Http\Controllers\CityController::class, 'byState'])->name('masters.cities.by-state');
        Route::get('cities/metro', [App\Http\Controllers\CityController::class, 'metroOnly'])->name('masters.cities.metro');
        
        // Property Masters
        Route::apiResource('property-types', App\Http\Controllers\PropertyTypeController::class);
        Route::get('property-types/{propertyTypeId}/statuses', [App\Http\Controllers\PropertyTypeController::class, 'statuses'])->name('masters.property-types.statuses');
        Route::apiResource('property-statuses', App\Http\Controllers\PropertyStatusController::class);
        
        // Amenity Masters
        Route::apiResource('amenity-categories', App\Http\Controllers\AmenityCategoryController::class);
        Route::apiResource('amenities', App\Http\Controllers\AmenityController::class);
        Route::get('amenities/by-category/{categoryId}', [App\Http\Controllers\AmenityController::class, 'byCategory'])->name('masters.amenities.by-category');
        
        // Specification Masters
        Route::apiResource('specification-categories', App\Http\Controllers\SpecificationCategoryController::class);
        Route::apiResource('specification-types', App\Http\Controllers\SpecificationTypeController::class);
        Route::get('specification-types/by-category/{categoryId}', [App\Http\Controllers\SpecificationTypeController::class, 'byCategory'])->name('masters.specification-types.by-category');
        
        // Lead Masters
        Route::apiResource('lead-sources', App\Http\Controllers\LeadSourceController::class);
        Route::apiResource('lead-statuses', App\Http\Controllers\LeadStatusController::class);
        Route::get('lead-statuses/pipeline', [App\Http\Controllers\LeadStatusController::class, 'pipeline'])->name('masters.lead-statuses.pipeline');
        Route::get('lead-statuses/final', [App\Http\Controllers\LeadStatusController::class, 'final'])->name('masters.lead-statuses.final');
        Route::apiResource('budget-ranges', App\Http\Controllers\BudgetRangeController::class);
        Route::apiResource('timelines', App\Http\Controllers\TimelineController::class);
        
        // Generic Masters
        Route::apiResource('generic-masters', App\Http\Controllers\GenericMasterController::class);
        Route::get('generic-masters/by-type/{type}', [App\Http\Controllers\GenericMasterController::class, 'byType'])->name('masters.generic.by-type');
        Route::get('generic-masters/types/list', [App\Http\Controllers\GenericMasterController::class, 'types'])->name('masters.generic.types');
        
        // Toggle Active Routes (for all masters)
        Route::post('{controller}/{id}/toggle', function ($controller, $id) {
            $controllerMap = [
                'countries' => App\Http\Controllers\CountryController::class,
                'states' => App\Http\Controllers\StateController::class,
                'cities' => App\Http\Controllers\CityController::class,
                'property-types' => App\Http\Controllers\PropertyTypeController::class,
                'property-statuses' => App\Http\Controllers\PropertyStatusController::class,
                'amenity-categories' => App\Http\Controllers\AmenityCategoryController::class,
                'amenities' => App\Http\Controllers\AmenityController::class,
                'specification-categories' => App\Http\Controllers\SpecificationCategoryController::class,
                'specification-types' => App\Http\Controllers\SpecificationTypeController::class,
                'lead-sources' => App\Http\Controllers\LeadSourceController::class,
                'lead-statuses' => App\Http\Controllers\LeadStatusController::class,
                'budget-ranges' => App\Http\Controllers\BudgetRangeController::class,
                'timelines' => App\Http\Controllers\TimelineController::class,
                'generic-masters' => App\Http\Controllers\GenericMasterController::class,
            ];
            
            if (!isset($controllerMap[$controller])) {
                abort(404);
            }
            
            return app($controllerMap[$controller])->toggleActive($id);
        })->name('masters.toggle');
    });

    // API Routes (For dropdowns - authenticated users)
    Route::prefix('api/v1/masters')->middleware('auth')->group(function () {
        // Location APIs
        Route::get('countries', [App\Http\Controllers\CountryController::class, 'index']);
        Route::get('states', [App\Http\Controllers\StateController::class, 'index']);
        Route::get('states/by-country/{countryId}', [App\Http\Controllers\StateController::class, 'byCountry']);
        Route::get('cities', [App\Http\Controllers\CityController::class, 'index']);
        Route::get('cities/by-state/{stateId}', [App\Http\Controllers\CityController::class, 'byState']);
        Route::get('cities/metro', [App\Http\Controllers\CityController::class, 'metroOnly']);
        
        // Property APIs
        Route::get('property-types', [App\Http\Controllers\PropertyTypeController::class, 'index']);
        Route::get('property-statuses', [App\Http\Controllers\PropertyStatusController::class, 'index']);
        Route::get('property-types/{propertyTypeId}/statuses', [App\Http\Controllers\PropertyTypeController::class, 'statuses']);
        
        // Amenity APIs
        Route::get('amenity-categories', [App\Http\Controllers\AmenityCategoryController::class, 'index']);
        Route::get('amenities', [App\Http\Controllers\AmenityController::class, 'index']);
        Route::get('amenities/by-category/{categoryId}', [App\Http\Controllers\AmenityController::class, 'byCategory']);
        
        // Lead APIs
        Route::get('lead-sources', [App\Http\Controllers\LeadSourceController::class, 'index']);
        Route::get('lead-statuses', [App\Http\Controllers\LeadStatusController::class, 'index']);
        Route::get('lead-statuses/pipeline', [App\Http\Controllers\LeadStatusController::class, 'pipeline']);
        Route::get('budget-ranges', [App\Http\Controllers\BudgetRangeController::class, 'index']);
        Route::get('timelines', [App\Http\Controllers\TimelineController::class, 'index']);
        
        // Generic Masters
        Route::get('generic/{type}', [App\Http\Controllers\GenericMasterController::class, 'byType']);
    });
});

