<?php

use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

/**
 * API routes
 */
Route::prefix('auth')->middleware('auth:api')->group(function () {

    /**
     * Logout the user
     */
    Route::post('logout', [LogoutController::class, 'logout']);

});
require __DIR__ . '/config/auth.php';
