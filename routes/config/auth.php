<?php

use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API routes
 */
Route::prefix('auth')->group(function() {
    /**
     * Register new user
     */
    Route::post('register', [RegisterController::class, 'register']);

    /**
     * Forgot Password
     */
    Route::post('forgot-password', [ForgetPasswordController::class, 'sendResetLinkEmail']);

    /**
     * Reset Password
     */
    Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword']);


    /**
     * Login new User
     */
    Route::post('login', [LoginController::class, 'login']);
});
