<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UpdateCurrentAuthUserController;
use App\Http\Controllers\GithubThemeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ThemeCustomizationController;
use Illuminate\Support\Facades\Route;

/**
 * API routes
 */
Route::prefix('auth')->group(function () {

    /*************************************************************** Authenticated Routes *************************************************************/

    /**
     * Routes that require authentication
     */
    Route::middleware('auth:api')->group(function () {
        /**
         * Logout the user
         */
        Route::post('logout', [LogoutController::class, 'logout']);

        /**
         * Update Current Authenticated User
         */
        Route::put('update', [UpdateCurrentAuthUserController::class, 'update']);

        /**
         * Show current authenticated user
         */
        Route::get('show', [AuthController::class, 'show']);

        /**
         * Delete current authenticated user
         */
        Route::delete('delete', [AuthController::class, 'delete']);

        /**
         * Show store
         */
        Route::get('/store', [StoreController::class, 'show']);

        /**
         * Theme routes
         */
        Route::prefix('themes')->group(function () {
            /**
             * Get themes
             */
            Route::get('/', [GithubThemeController::class, 'index']);

            /**
             * Test a specific theme
             */
            Route::get('/{themeName}/test', [GithubThemeController::class, 'testTheme']);

            /**
             * Apply a theme to the store
             */
            Route::post('/{themeName}/apply', [GithubThemeController::class, 'applyTheme']);

            /**
             * Serve theme files
             */
            Route::get('/user/{userId}/{themeName}/{filePath?}', [StoreController::class, 'serveThemeFile'])
                ->where('filePath', '.*');

            /**
             * Theme customization routes
             */
            Route::prefix('customization')->group(function () {
                /**
                 * Get current theme
                 */
                Route::get('current', [ThemeCustomizationController::class, 'getCurrentTheme']);

                /**
                 * Get customization options
                 */
                Route::get('options', [ThemeCustomizationController::class, 'getCustomizationOptions']);

                /**
                 * Update customization
                 */
                Route::put('update', [ThemeCustomizationController::class, 'updateCustomization']);

                /**
                 * Reset customization
                 */
                Route::post('reset', [ThemeCustomizationController::class, 'resetCustomization']);
            });
        });
    });
});

/**
 * Store theme routes (accessible via custom domain)
 */
Route::domain('{domain}')->group(function () {
    Route::get('/{path?}', [StoreController::class, 'serveStoreTheme'])
        ->where('path', '.*')
        ->name('store.theme');
});

/**
 * Auth Routes
 */
require __DIR__ . '/config/auth.php';

/**
 * Forget Password Routes
 */
require __DIR__ . '/config/password.php';
