<?php

namespace App\Services\Facades\Auth;

use Illuminate\Support\Facades\Facade;

class AuthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'AuthService';
    }
}
