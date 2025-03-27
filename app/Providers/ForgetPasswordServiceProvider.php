<?php

namespace App\Providers;

use App\Services\Services\Auth\ForgetPasswordService;
use Illuminate\Support\ServiceProvider;

class ForgetPasswordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('ForgetPasswordService', ForgetPasswordService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
