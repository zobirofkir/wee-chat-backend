<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UpdateCurrentAuthUserController;
use Illuminate\Support\Facades\Route;

/**
 * API routes
 */
Route::prefix('auth')->middleware('auth:api')->group(function () {

    /**
     * Logout the user
     */
    Route::post('logout', [LogoutController::class, 'logout']);

    /**
     * Update Current Authenticated User
     */
    Route::put('update', [UpdateCurrentAuthUserController::class, 'update']);

});
require __DIR__ . '/config/auth.php';
