<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Facades\Auth\ForgetPasswordFacade;
use Illuminate\Http\Request;

class ForgetPasswordController extends Controller
{
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        return ForgetPasswordFacade::sendResetLinkEmail($request);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return ForgetPasswordFacade::showResetForm($request, $token);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return ForgetPasswordFacade::resetPassword($request);
    }
}
