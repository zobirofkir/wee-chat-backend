<?php

namespace App\Services\Facades\Auth;

use Illuminate\Support\Facades\Facade;

class ForgetPasswordFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ForgetPasswordService';
    }
}
