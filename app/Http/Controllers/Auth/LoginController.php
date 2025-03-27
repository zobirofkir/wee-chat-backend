<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Services\Facades\Auth\LoginFacade;

class LoginController extends Controller
{
    /**
     * Login the user
     *
     * @param LoginRequest $request
     * @return LoginResource
     */
    public function login(LoginRequest $request) : LoginResource
    {
        $user = LoginFacade::login($request);
        return LoginResource::make($user);
    }
}
