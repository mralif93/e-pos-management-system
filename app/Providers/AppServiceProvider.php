<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use App\Models\User;

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
        Paginator::defaultView('vendor.pagination.tailwind');

        Gate::define('access-pos', function (User $user) {
            return in_array($user->role, ['Cashier', 'Manager']);
        });

        Gate::define('access-admin', function (User $user) {
            return in_array($user->role, ['Admin', 'Super Admin']);
        });
    }
}
