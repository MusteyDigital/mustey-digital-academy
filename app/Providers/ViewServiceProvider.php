<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share notifications with all dashboard views
        View::composer(['dashboards.*'], function ($view) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $view->with('notifications', $user->notifications()->latest()->take(10)->get());
            $view->with('unreadCount', $user->unreadNotifications()->count());
        });
    }
}
