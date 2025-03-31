<?php

namespace App\Services\Constructors;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface ThemeCustomizationConstructor
{
    /**
     * Get theme customization options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomizationOptions(Request $request) : JsonResponse;

    /**
     * Update theme customization
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCustomization(Request $request) : JsonResponse;

    /**
     * Reset theme customization to default
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetCustomization(Request $request) : JsonResponse;
}
