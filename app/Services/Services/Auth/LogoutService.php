<?php

namespace App\Services\Services\Auth;

use App\Services\Constructors\Auth\LogoutConstructor;
use Illuminate\Support\Facades\Auth;

class LogoutService implements LogoutConstructor
{
    public function logout(): bool
    {
        return Auth::user()->token()->revoke();
    }
}
