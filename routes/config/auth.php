<?php

use App\Http\Controllers\LoginController;
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
     * Login new User
     */
    Route::post('login', [LoginController::class, 'login']);
});
