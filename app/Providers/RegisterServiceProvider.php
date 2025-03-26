<?php

namespace App\Providers;

use App\Http\Resources\RegisterResource;
use App\Services\Services\RegisterService;
use Illuminate\Support\ServiceProvider;

class RegisterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('RegisterService', RegisterService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
