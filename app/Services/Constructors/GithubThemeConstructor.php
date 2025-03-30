<?php

namespace App\Services\Constructors;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface GithubThemeConstructor
{
    /**
     * List All Themes
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse;

    /**
     * Generate a test URL for a theme
     *
     * @param [type] $themeName
     * @return void
     */
    public function generateTestUrl($themeName) : string;

    /**
     * Get a specific theme for testing
     *
     * @param [type] $themeName
     * @return JsonResponse
     */
    public function getTestTheme($themeName) : JsonResponse;

    /**
     * Clear the cache for all themes
     *
     * @return JsonResponse
     */
    public function clearCache() : JsonResponse;

    /**
     * Apply a theme to a user's store
     *
     * @param Request $request
     * @param string $themeName
     * @return JsonResponse
     */
    public function applyTheme(Request $request, string $themeName) : JsonResponse;
}
