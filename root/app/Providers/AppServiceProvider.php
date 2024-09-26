<?php

namespace App\Providers;

use App\Models\Admin;
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
        // [Laravel10] App\Providers\AuthServiceProvider => [Laravel11] App\Providers\AppServiceProvider
        // 参考: Laravel 11.x 認可 https://readouble.com/laravel/11.x/ja/authorization.html#writing-gates
        // 参考: Laravel 10.x 認可 https://readouble.com/laravel/10.x/ja/authorization.html#writing-gates

        Gate::define('is_system_admin', function (Admin $user) {
            return $user->is_system_admin;
        });
    }
}
