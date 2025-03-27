<?php

namespace App\Services\Facades\Auth;

use Illuminate\Support\Facades\Facade;

class UpdateCurrentAuthUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'UpdateCurrentAuthUserService';
    }
}
