<?php

namespace App\Services\Services\Store;

use App\Services\Constructors\ThemeCustomizationConstructor;
use App\Http\Resources\ThemeCustomizationResource;
use App\Http\Resources\ThemeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ThemeCustomizationService implements ThemeCustomizationConstructor
{
    /**
     * Get theme customization options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomizationOptions(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store || !$store->theme) {
            return response()->json(
                ThemeCustomizationResource::error('No active theme found for this store'),
                404
            );
        }

        $customizationPath = $this->getCustomizationPath($user->id, $store->theme);
        $defaultOptions = $this->getDefaultCustomizationOptions($store->theme);

        if (Storage::exists($customizationPath)) {
            $customOptions = json_decode(Storage::get($customizationPath), true);
            return response()->json(new ThemeCustomizationResource(array_merge($defaultOptions, $customOptions)));
        }

        return response()->json(new ThemeCustomizationResource($defaultOptions));
    }

    /**
     * Update theme customization
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCustomization(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store || !$store->theme) {
            return response()->json(
                ThemeCustomizationResource::error('No active theme found for this store'),
                404
            );
        }

        $customizationPath = $this->getCustomizationPath($user->id, $store->theme);
        $customOptions = $request->input('options');

        if (!$customOptions) {
            return response()->json(
                ThemeCustomizationResource::error('No customization options provided'),
                400
            );
        }

        try {
            Storage::put($customizationPath, json_encode($customOptions, JSON_PRETTY_PRINT));

            return response()->json(new ThemeCustomizationResource($customOptions));
        } catch (\Exception $e) {
            Log::error('Failed to update theme customization: ' . $e->getMessage());
            return response()->json(
                ThemeCustomizationResource::error('Failed to update theme customization'),
                500
            );
        }
    }

    /**
     * Reset theme customization to default
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetCustomization(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store || !$store->theme) {
            return response()->json(
                ThemeCustomizationResource::error('No active theme found for this store'),
                404
            );
        }

        $customizationPath = $this->getCustomizationPath($user->id, $store->theme);

        try {
            if (Storage::exists($customizationPath)) {
                Storage::delete($customizationPath);
            }

            $defaultOptions = $this->getDefaultCustomizationOptions($store->theme);

            return response()->json(new ThemeCustomizationResource($defaultOptions));
        } catch (\Exception $e) {
            Log::error('Failed to reset theme customization: ' . $e->getMessage());
            return response()->json(
                ThemeCustomizationResource::error('Failed to reset theme customization'),
                500
            );
        }
    }

    /**
     * Get customization file path
     *
     * @param int $userId
     * @param string $themeName
     * @return string
     */
    protected function getCustomizationPath(int $userId, string $themeName) : string
    {
        return "themes/user_{$userId}/{$themeName}/customization.json";
    }

    /**
     * Get default customization options for a theme
     *
     * @param string $themeName
     * @return array
     */
    protected function getDefaultCustomizationOptions(string $themeName) : array
    {
        return [
            'colors' => [
                'primary' => '#007bff',
                'secondary' => '#6c757d',
                'background' => '#ffffff',
                'text' => '#212529'
            ],
            'typography' => [
                'font_family' => 'Arial, sans-serif',
                'font_size' => '16px',
                'line_height' => '1.5'
            ],
            'layout' => [
                'container_width' => '1200px',
                'spacing' => '1rem'
            ]
        ];
    }

    /**
     * Get current theme information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentTheme(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store || !$store->theme) {
            return response()->json(
                ThemeResource::error('No active theme found for this store'),
                404
            );
        }

        $themePath = "themes/user_{$user->id}/{$store->theme}";
        $themeInfoPath = "{$themePath}/theme-info.json";

        $themeInfo = [];
        if (Storage::exists($themeInfoPath)) {
            $themeInfo = json_decode(Storage::get($themeInfoPath), true);
        }

        $themeData = [
            'name' => $store->theme,
            'applied_at' => $store->theme_applied_at,
            'storage_path' => $store->theme_storage_path,
            'preview_url' => url("storage/themes/user_{$user->id}/{$store->theme}/index.html"),
            'info' => $themeInfo
        ];

        return response()->json(ThemeResource::make($themeData));
    }
}
