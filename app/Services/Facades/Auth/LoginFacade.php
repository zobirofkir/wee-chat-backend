<?php

namespace App\Services\Facades\Auth;

use Illuminate\Support\Facades\Facade;

class LoginFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LoginService';
    }
}
