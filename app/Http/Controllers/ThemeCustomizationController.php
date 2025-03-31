<?php

namespace App\Http\Controllers;

use App\Services\Facades\ThemeCustomizationFacade;
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
    public function updateCustomization(Request $request) : JsonResponse
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
}
