<?php

namespace App\Services\Facades;

use App\Services\Services\Store\ThemeCustomizationService;
use Illuminate\Support\Facades\Facade;

class ThemeCustomizationFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return "ThemeCustomizationService";
    }
}
