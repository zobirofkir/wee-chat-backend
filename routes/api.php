<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UpdateCurrentAuthUserController;
use Illuminate\Support\Facades\Route;

/**
 * API routes
 */
Route::prefix('auth')->group(function () {

    /**
     * Forgot Password
     */
    Route::post('forgot-password', [ForgetPasswordController::class, 'sendResetLinkEmail']);

    /**
     * Reset Password
     */
    Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword']);

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
    });
});

require __DIR__ . '/config/auth.php';
