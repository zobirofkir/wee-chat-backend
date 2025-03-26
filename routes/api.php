<?php

use App\Http\Controllers\LogoutController;
use Illuminate\Routing\Route;

/**
 * API routes
 */
Route::middleware('auth:api')->prefix('auth')->group(function() {
    
    /**
     * Logout the user
     */
    Route::post('logout', [LogoutController::class, 'logout']);
});

require __DIR__ . '/config/auth.php';
