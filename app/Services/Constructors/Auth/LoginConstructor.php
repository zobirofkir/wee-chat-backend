<?php

namespace App\Services\Constructors\Auth;

use App\Http\Requests\LoginRequest;
use App\Models\User;

interface LoginConstructor
{
    /**
     * Login
     *
     * @param LoginRequest $request
     * @return User
     */
    public function login(LoginRequest $request) : User;
}
