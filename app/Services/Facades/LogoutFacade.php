<?php

namespace App\Services\Facades;

use Illuminate\Support\Facades\Facade;

class LogoutFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LogoutService';
    }
}