<?php

namespace App\Http\Controllers;

use App\Services\Facades\Store\GithubThemeFacade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GithubThemeController extends Controller
{
    /**
     * Get all themes
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        return GithubThemeFacade::index();
    }

    /**
     * Get a specific theme for testing
     *
     * @param string $themeName
     * @return \Illuminate\Http\JsonResponse
     */
    public function testTheme($themeName) : JsonResponse
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
    public function applyTheme(Request $request, string $themeName) : JsonResponse
    {
        return GithubThemeFacade::applyTheme($request, $themeName);
    }
}
