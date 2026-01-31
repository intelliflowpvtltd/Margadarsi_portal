<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

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

    // Companies - Resourceful routes
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    Route::post('companies/{id}/restore', [\App\Http\Controllers\CompanyController::class, 'restore'])->name('companies.restore');
    Route::delete('companies/{id}/force', [\App\Http\Controllers\CompanyController::class, 'forceDelete'])->name('companies.force-delete');

    // Projects - Resourceful routes
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    Route::post('projects/{id}/restore', [\App\Http\Controllers\ProjectController::class, 'restore'])->name('projects.restore');
    Route::delete('projects/{id}/force', [\App\Http\Controllers\ProjectController::class, 'forceDelete'])->name('projects.force-delete');

    // Test route for Projects API
    Route::get('/projects-test', function () {
        return view('projects.test');
    })->name('projects.test');

    // Roles - Resourceful routes
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::post('roles/{id}/restore', [\App\Http\Controllers\RoleController::class, 'restore'])->name('roles.restore');
    Route::delete('roles/{id}/force', [\App\Http\Controllers\RoleController::class, 'forceDelete'])->name('roles.force-delete');
    // Permissions Management for Roles
    Route::get('roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'editPermissions'])->name('roles.permissions');
    Route::put('roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // Users
    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

    // Leads
    Route::get('/leads', function () {
        return view('leads.index');
    })->name('leads.index');
});
