<?php

namespace App\Services\Services\Store;

use App\Services\Constructors\Store\ThemeCustomizationConstructor;
use App\Http\Resources\ThemeCustomizationResource;
use App\Http\Resources\ThemeResource;
use App\Services\Services\Store\Traits\ThemeCustomizationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ThemeCustomizationService implements ThemeCustomizationConstructor
{
    /**
     * Theme customization trait
     */
    use ThemeCustomizationTrait;

    /**
     * Get theme customization options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomizationOptions(Request $request): JsonResponse
    {
        $user = $request->user();

        $store = $user->store;

        $customizationPath = $this->getCustomizationPath($user->id, $store->theme);
        $defaultOptions = $this->getDefaultCustomizationOptions($store->theme);
        $customOptions = $this->getCustomOptions($customizationPath);

        return response()->json(ThemeCustomizationResource::make(array_merge($defaultOptions, $customOptions)));
    }

    /**
     * Update theme customization
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateThemeCustomization(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $customOptions = $request->input('options');

        return $this->saveCustomization($user->id, $store->theme, $customOptions);
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

        $customizationPath = $this->getCustomizationPath($user->id, $store->theme);

        if (Storage::exists($customizationPath)) {
            Storage::delete($customizationPath);
        }

        $defaultOptions = $this->getDefaultCustomizationOptions($store->theme);
        return response()->json(ThemeCustomizationResource::make($defaultOptions));
    }

    /**
     * Get current theme information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentTheme(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $themeData = $this->getThemeData($user, $store);

        return response()->json(ThemeResource::make($themeData));
    }

    /**
     * Update specific theme file content
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateThemeFile(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $filePath = $request->input('file_path');
        $content = $request->input('content');
        $section = $request->input('section');
        $element = $request->input('element');

        $themePath = "themes/user_{$user->id}/{$store->theme}";
        $fullPath = Storage::disk('public')->path("{$themePath}/{$filePath}");

        $fileContent = Storage::disk('public')->get("{$themePath}/{$filePath}");

        if ($section && $element) {
            $fileContent = $this->updateSpecificElement($fileContent, $section, $element, $content);
        } else {
            $fileContent = $content;
        }

        Storage::disk('public')->put("{$themePath}/{$filePath}", $fileContent);

        return response()->json([
            'file_path' => $filePath
        ]);
    }

    /**
     * Get theme file content
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getThemeFile(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $filePath = $request->input('file_path');

        if (!$filePath) {

            /**
             * If no file path provided, return list of available files
            */

            $themePath = "themes/user_{$user->id}/{$store->theme}";

            $files = Storage::disk('public')->allFiles($themePath);
            $availableFiles = [];

            foreach ($files as $file) {
                if (basename($file) !== 'theme-info.json') {
                    $relativePath = str_replace($themePath . '/', '', $file);
                    $availableFiles[] = [
                        'name' => basename($file),
                        'path' => $relativePath,
                        'type' => pathinfo($file, PATHINFO_EXTENSION),
                        'size' => Storage::disk('public')->size($file),
                        'last_modified' => Storage::disk('public')->lastModified($file)
                    ];
                }
            }

            return response()->json([
                'data' => $availableFiles,
            ]);
        }

        $themePath = "themes/user_{$user->id}/{$store->theme}";

        $content = Storage::disk('public')->get("{$themePath}/{$filePath}");
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

        return response()->json([
            'success' => true,
            'content' => $content,
            'file_info' => [
                'path' => $filePath,
                'type' => $fileType,
                'size' => Storage::disk('public')->size("{$themePath}/{$filePath}"),
                'last_modified' => Storage::disk('public')->lastModified("{$themePath}/{$filePath}")
            ],
            'theme_path' => $themePath
        ]);
    }

    /**
     * List all HTML files in the theme directory
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listHtmlFiles(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $themePath = "themes/user_{$user->id}/{$store->theme}";

        $files = Storage::disk('public')->allFiles($themePath);
        $htmlFiles = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                $relativePath = str_replace($themePath . '/', '', $file);
                $htmlFiles[] = [
                    'name' => basename($file),
                    'path' => $relativePath,
                    'full_path' => $file,
                    'size' => Storage::disk('public')->size($file),
                    'last_modified' => Storage::disk('public')->lastModified($file)
                ];
            }
        }

        return response()->json([
            'files' => $htmlFiles,
            'total' => count($htmlFiles)
        ]);
    }
}
