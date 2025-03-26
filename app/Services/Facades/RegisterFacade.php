<?php

namespace App\Services\Facades;

use Illuminate\Support\Facades\Facade;

class RegisterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'RegisterService';
    }
}