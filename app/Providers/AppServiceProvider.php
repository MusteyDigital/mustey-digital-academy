<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Admin only
        Gate::define('admin', function ($user) {
            return ($user->role ?? 'student') === 'admin';
        });

        // Manage users (Admin only)
        Gate::define('manage-users', function ($user) {
            return ($user->role ?? 'student') === 'admin';
        });

        // Manage courses (Admin + Instructor)
        Gate::define('manage-courses', function ($user) {
            $role = $user->role ?? 'student';
            return in_array($role, ['admin', 'instructor'], true);
        });
    }
}
