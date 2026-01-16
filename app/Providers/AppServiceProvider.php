<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        if (config('filament-shield.super_admin.enabled')) {
            Gate::before(function ($user, $ability) {
                $roleName = config('filament-shield.super_admin.name', 'super_admin');

                return method_exists($user, 'hasRole') && $user->hasRole($roleName) ? true : null;
            });
        }
    }
}
