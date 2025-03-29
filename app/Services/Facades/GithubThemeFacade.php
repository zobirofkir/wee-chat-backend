<?php

namespace App\Services\Facades;

use Illuminate\Support\Facades\Facade;

class GithubThemeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GithubThemeService';
    }
}