<?php

namespace App\Services\Facades\Auth;

use Illuminate\Support\Facades\Facade;

class RegisterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'RegisterService';
    }
}
