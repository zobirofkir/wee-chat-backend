<?php

namespace App\Providers;

use App\Services\Services\Auth\LogoutService;
use Illuminate\Support\ServiceProvider;

class LogoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('LogoutService', LogoutService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
