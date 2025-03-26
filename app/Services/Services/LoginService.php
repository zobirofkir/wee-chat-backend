<?php

namespace App\Services\Services;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Services\Constructors\LoginConstructor;
use Illuminate\Support\Facades\Auth;

class LoginService implements LoginConstructor
{
    public function login(LoginRequest $request): LoginResource
    {
        $validateDdata = $request->validated();
        if (!Auth::attempt($validateDdata)) {
            return new LoginResource(['error' => 'Invalid credentials']);
        }
        $token = Auth::user()->createToken('token')->plainTextToken;
        return new LoginResource(['token' => $token]);
    }
}
