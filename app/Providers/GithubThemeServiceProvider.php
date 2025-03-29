<?php

namespace App\Providers;

use App\Services\Services\GithubThemeService;
use Illuminate\Support\ServiceProvider;

class GithubThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('GithubThemeService', GithubThemeService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
