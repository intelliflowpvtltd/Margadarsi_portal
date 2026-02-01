<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register model observers
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Register Gate for @can directives to work with hasPermission()
        // This connects Blade @can('permission.name') to User::hasPermission()
        Gate::before(function ($user, $ability) {
            // Super admin bypasses all permission checks
            if ($user->isSuperAdmin()) {
                return true;
            }

            // Check if user has the specific permission
            if ($user->hasPermission($ability)) {
                return true;
            }

            // Return null to continue to other checks
            return null;
        });
    }
}
