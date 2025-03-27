<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Auth\PasswordResetResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Config;

class ForgetPasswordController extends Controller
{
    public function __construct()
    {
        Config::set('auth.defaults.passwords', 'users');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \App\Http\Requests\Auth\ForgotPasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink([
            'email' => $request['email']
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
            'errors' => session()->get('errors') ?: new \Illuminate\Support\ViewErrorBag
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \App\Http\Requests\Auth\ResetPasswordRequest  $request
     * @return \Illuminate\Http\Response
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

        return $status === Password::PASSWORD_RESET
            ? redirect()->back()->with('success', 'Your password has been reset!')
            : back()->withErrors(['email' => __($status)]);
    }
}
