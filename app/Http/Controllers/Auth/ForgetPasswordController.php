<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Auth\PasswordResetResource;
use Illuminate\Support\Facades\Password;

class ForgetPasswordController extends Controller
{
    /**
     * Send a reset link to the given user.
     *
     * @param  \App\Http\Requests\Auth\ForgotPasswordRequest  $request
     * @return \App\Http\Resources\Auth\PasswordResetResource
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink([
            'email' => $request['email']
        ]);

        return new PasswordResetResource([
            'status' => $status === Password::RESET_LINK_SENT ? 'success' : 'error',
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Reset link sent to your email'
                : 'Unable to send reset link'
        ]);
    }

    /**
     * Reset the user's password.
     *
     * @param  \App\Http\Requests\Auth\ResetPasswordRequest  $request
     * @return \App\Http\Resources\Auth\PasswordResetResource
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset([
            'email' => $request['email'],
            'password' => $request['password'],
            'password_confirmation' => $request['password_confirmation'],
            'token' => $request['token']
        ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );

        return new PasswordResetResource([
            'status' => $status === Password::PASSWORD_RESET ? 'success' : 'error',
            'message' => $status === Password::PASSWORD_RESET
                ? 'Password has been reset'
                : 'Unable to reset password'
        ]);
    }
}
