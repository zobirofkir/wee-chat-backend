<?php

namespace App\Http\Controllers;

use App\Services\Facades\GithubThemeFacade;
use Illuminate\Http\Request;

class GithubThemeController extends Controller
{
    public function index()
    {
        return GithubThemeFacade::index();
    }

    /**
     * Get a specific theme for testing
     *
     * @param string $themeName
     * @return \Illuminate\Http\JsonResponse
     */
    public function testTheme($themeName)
    {
        return GithubThemeFacade::getTestTheme($themeName);
    }

    /**
     * Apply a theme to the store
     *
     * @param Request $request
     * @param string $themeName
     * @return void
     */
    public function applyTheme(Request $request, string $themeName)
    {
        return GithubThemeFacade::applyTheme($request, $themeName);
    }
}
