<?php

namespace App\Services\Facades\Store;

use Illuminate\Support\Facades\Facade;

class GithubThemeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GithubThemeService';
    }
}