<?php

namespace App\Services\Services;

use App\Services\Constructors\LogoutConstructor;
use Illuminate\Support\Facades\Auth;

class LogoutService implements LogoutConstructor
{
    public function logout(): bool
    {
        return Auth::user()->token()->revoke();
    }
}
