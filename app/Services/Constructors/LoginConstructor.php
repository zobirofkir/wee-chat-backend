<?php

namespace App\Services\Constructors;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;

interface LoginConstructor
{
    /**
     * Login
     *
     * @param LoginRequest $request
     * @return LoginResource
     */
    public function login(LoginRequest $request) : LoginResource;
}
