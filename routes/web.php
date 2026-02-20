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
    // Create route MUST come first to prevent 'create' being matched as role ID
    Route::middleware('permission:roles.create')->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->whereNumber('role');
        Route::get('roles/{id}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions');
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
    // Create route MUST come first to prevent 'create' being matched as department ID
    Route::middleware('permission:departments.create')->group(function () {
        Route::get('departments/create', [App\Http\Controllers\DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments', [App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
    });
    Route::middleware('permission:departments.view')->group(function () {
        Route::get('departments', [App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'show'])->name('departments.show')->whereNumber('department');
    });
    Route::middleware('permission:departments.stats')->get('departments/{department}/stats', [App\Http\Controllers\DepartmentController::class, 'stats'])->name('departments.stats');
    Route::middleware('permission:departments.update')->group(function () {
        Route::get('departments/{department}/edit', [App\Http\Controllers\DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'update'])->name('departments.update');
        Route::patch('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'update']);
    });
    Route::middleware('permission:departments.delete')->delete('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.destroy');

    // ==================== USERS ====================
    // Create routes MUST come BEFORE {user} routes to prevent 'create' matching as ID
    Route::middleware('permission:users.create')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')
            ->whereNumber('user');
    });
    Route::middleware('permission:users.update')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')
            ->whereNumber('user');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}', [UserController::class, 'update']);
    });
    Route::middleware('permission:users.delete')->delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::middleware('permission:users.restore')->post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::middleware('permission:users.force-delete')->delete('users/{id}/force', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::middleware('permission:users.assign-projects')->post('users/{user}/projects', [UserController::class, 'assignProjects'])->name('users.assign-projects');


    // ==================== LEADS ====================
    Route::middleware('permission:leads.view')->group(function () {
        Route::get('/leads', function () {
            return view('leads.index');
        })->name('leads.index');
        Route::get('/leads/create', function () {
            return view('leads.create');
        })->name('leads.create');
        Route::get('/leads/{lead}', function ($lead) {
            return view('leads.show', ['leadId' => $lead]);
        })->name('leads.show');
        Route::get('/leads/{lead}/edit', function ($lead) {
            return view('leads.edit', ['leadId' => $lead]);
        })->name('leads.edit');
    });

    // ==================== MASTER DATA MANAGEMENT ====================
    
    // View Routes (Return Blade views for Master Data pages - HTML only)
    // These routes use content negotiation to serve views only for browser requests
    Route::prefix('admin/masters')->middleware('auth')->group(function () {
        // All masters index/dashboard
        Route::get('/', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Use specific resource endpoints']);
            }
            return view('masters.index');
        })->name('masters.index');
        
        // Location Masters Views
        Route::get('/countries', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\CountryController::class)->index($request);
            }
            return view('masters.countries');
        })->name('masters.countries.index');
        Route::get('/states', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\StateController::class)->index($request);
            }
            return view('masters.states');
        })->name('masters.states.index');
        Route::get('/cities', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\CityController::class)->index($request);
            }
            return view('masters.cities');
        })->name('masters.cities.index');
        
        // Property Masters Views
        Route::get('/property-types', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\PropertyTypeController::class)->index($request);
            }
            return view('masters.property-types');
        })->name('masters.property-types.index');
        Route::get('/property-statuses', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\PropertyStatusController::class)->index($request);
            }
            return view('masters.property-statuses');
        })->name('masters.property-statuses.index');
        
        // Amenity Masters Views
        Route::get('/amenity-categories', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\AmenityCategoryController::class)->index($request);
            }
            return view('masters.amenity-categories');
        })->name('masters.amenity-categories.index');
        Route::get('/amenities', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\AmenityController::class)->index($request);
            }
            return view('masters.amenities');
        })->name('masters.amenities.index');
        
        // Specification Masters Views
        Route::get('/specification-categories', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\SpecificationCategoryController::class)->index($request);
            }
            return view('masters.specification-categories');
        })->name('masters.specification-categories.index');
        Route::get('/specification-types', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\SpecificationTypeController::class)->index($request);
            }
            return view('masters.specification-types');
        })->name('masters.specification-types.index');
        
        // Lead Masters Views
        Route::get('/lead-sources', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\LeadSourceController::class)->index($request);
            }
            return view('masters.lead-sources');
        })->name('masters.lead-sources.index');
        Route::get('/lead-statuses', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\LeadStatusController::class)->index($request);
            }
            return view('masters.lead-statuses');
        })->name('masters.lead-statuses.index');
        Route::get('/budget-ranges', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\BudgetRangeController::class)->index($request);
            }
            return view('masters.budget-ranges');
        })->name('masters.budget-ranges.index');
        Route::get('/timelines', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\TimelineController::class)->index($request);
            }
            return view('masters.timelines');
        })->name('masters.timelines.index');
        
        // Generic Masters Views
        Route::get('/generic-masters', function (\Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->ajax()) {
                return app(\App\Http\Controllers\GenericMasterController::class)->index($request);
            }
            return view('masters.generic-masters');
        })->name('masters.generic-masters.index');
    });

    // API Routes (Protected by permissions - CRUD operations except index)
    // Index is handled by view routes with content negotiation
    Route::prefix('admin/masters')->middleware('permission:masters.manage')->group(function () {
        
        // Location Masters (exclude index - handled by view routes)
        Route::apiResource('countries', App\Http\Controllers\CountryController::class)->except(['index']);
        Route::apiResource('states', App\Http\Controllers\StateController::class)->except(['index']);
        Route::get('states/by-country/{countryId}', [App\Http\Controllers\StateController::class, 'byCountry'])->name('masters.states.by-country');
        Route::apiResource('cities', App\Http\Controllers\CityController::class)->except(['index']);
        Route::get('cities/by-state/{stateId}', [App\Http\Controllers\CityController::class, 'byState'])->name('masters.cities.by-state');
        Route::get('cities/metro', [App\Http\Controllers\CityController::class, 'metroOnly'])->name('masters.cities.metro');
        
        // Property Masters
        Route::apiResource('property-types', App\Http\Controllers\PropertyTypeController::class)->except(['index']);
        Route::get('property-types/{propertyTypeId}/statuses', [App\Http\Controllers\PropertyTypeController::class, 'statuses'])->name('masters.property-types.statuses');
        Route::apiResource('property-statuses', App\Http\Controllers\PropertyStatusController::class)->except(['index']);
        
        // Amenity Masters
        Route::apiResource('amenity-categories', App\Http\Controllers\AmenityCategoryController::class)->except(['index']);
        Route::apiResource('amenities', App\Http\Controllers\AmenityController::class)->except(['index']);
        Route::get('amenities/by-category/{categoryId}', [App\Http\Controllers\AmenityController::class, 'byCategory'])->name('masters.amenities.by-category');
        
        // Specification Masters
        Route::apiResource('specification-categories', App\Http\Controllers\SpecificationCategoryController::class)->except(['index']);
        Route::apiResource('specification-types', App\Http\Controllers\SpecificationTypeController::class)->except(['index']);
        Route::get('specification-types/by-category/{categoryId}', [App\Http\Controllers\SpecificationTypeController::class, 'byCategory'])->name('masters.specification-types.by-category');
        
        // Lead Masters
        Route::apiResource('lead-sources', App\Http\Controllers\LeadSourceController::class)->except(['index']);
        Route::apiResource('lead-statuses', App\Http\Controllers\LeadStatusController::class)->except(['index']);
        Route::get('lead-statuses/pipeline', [App\Http\Controllers\LeadStatusController::class, 'pipeline'])->name('masters.lead-statuses.pipeline');
        Route::get('lead-statuses/final', [App\Http\Controllers\LeadStatusController::class, 'final'])->name('masters.lead-statuses.final');
        Route::apiResource('budget-ranges', App\Http\Controllers\BudgetRangeController::class)->except(['index']);
        Route::apiResource('timelines', App\Http\Controllers\TimelineController::class)->except(['index']);
        
        // Generic Masters
        Route::apiResource('generic-masters', App\Http\Controllers\GenericMasterController::class)->except(['index']);
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

