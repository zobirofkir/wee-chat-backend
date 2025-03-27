<?php

namespace App\Providers;

use App\Services\Services\Auth\UpdateCurrentAuthUserService;
use Illuminate\Support\ServiceProvider;

class UpdateCurrentAuthUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('UpdateCurrentAuthUserService', UpdateCurrentAuthUserService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
