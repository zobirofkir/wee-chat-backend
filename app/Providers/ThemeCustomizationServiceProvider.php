<?php

namespace App\Providers;

use App\Services\Services\Store\ThemeCustomizationService;
use Illuminate\Support\ServiceProvider;

class ThemeCustomizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton("ThemeCustomizationService", ThemeCustomizationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
