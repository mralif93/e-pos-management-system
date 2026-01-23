<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Assuming User model is used for authentication

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
        Gate::define('access-pos', function (User $user) {
            return in_array($user->role, ['Cashier', 'Manager', 'Admin', 'Super Admin']);
        });
    }
}
