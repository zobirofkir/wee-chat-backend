<?php

namespace App\Services\Constructors\Auth;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;

interface ForgetPasswordConstructor
{
    public function sendResetLinkEmail(ForgotPasswordRequest $request);

    public function showResetForm(Request $request, $token = null);

    public function resetPassword(ResetPasswordRequest $request);

    public function __construct();
}
