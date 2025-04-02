<?php

namespace App\Http\Controllers;

use App\Services\Facades\Store\ThemeCustomizationFacade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ThemeCustomizationController extends Controller
{
    /**
     * Get theme customization options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomizationOptions(Request $request) : JsonResponse
    {
        return ThemeCustomizationFacade::getCustomizationOptions($request);
    }

    /**
     * Update theme customization
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateThemeCustomization(Request $request) : JsonResponse
    {
        return ThemeCustomizationFacade::updateCustomization($request);
    }

    /**
     * Reset theme customization to default
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetCustomization(Request $request) : JsonResponse
    {
        return ThemeCustomizationFacade::resetCustomization($request);
    }

    /**
     * Get current theme information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentTheme(Request $request) : JsonResponse
    {
        return ThemeCustomizationFacade::getCurrentTheme($request);
    }

    /**
     * Get theme file content
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getThemeFile(Request $request) : JsonResponse
    {
        return ThemeCustomizationFacade::getThemeFile($request);
    }

    /**
     * Update theme file content
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateThemeFile(Request $request) : JsonResponse
    {
        return ThemeCustomizationFacade::updateThemeFile($request);
    }

    /**
     * List all HTML files in the theme directory
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listHtmlFiles(Request $request) : JsonResponse
    {
        return ThemeCustomizationFacade::listHtmlFiles($request);
    }
}
