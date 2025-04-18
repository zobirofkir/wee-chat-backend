<?php

namespace App\Services\Facades\Store;

use Illuminate\Support\Facades\Facade;

class StoreFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'StoreService';
    }
}