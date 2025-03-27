<?php

use App\Http\Controllers\Auth\ForgetPasswordController;
use Illuminate\Support\Facades\Route;


/**
 * Process the forgot password request
 */
Route::post('/password/email', [ForgetPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email.post');

/**
 * Show the reset password form
 */
Route::get('/password/reset/{token}', [ForgetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

/**
 * Process the reset password request
 */
Route::post('/password/reset', [ForgetPasswordController::class, 'resetPassword'])
    ->name('password.update');

Route::prefix('auth')->group(function () {
    /**
     * Send password reset link
     */
    Route::post('forgot-password', [ForgetPasswordController::class, 'sendResetLinkEmail']);

    /**
     * Reset password
     */
    Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword']);
});
