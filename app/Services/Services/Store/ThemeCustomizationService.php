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

        try {
            $themePath = "themes/user_{$user->id}/{$store->theme}";
            $fullPath = Storage::disk('public')->path("{$themePath}/{$filePath}");

            if (!Storage::disk('public')->exists("{$themePath}/{$filePath}")) {
                return response()->json(
                    ThemeCustomizationResource::error('Theme file not found'),
                    404
                );
            }

            $fileContent = Storage::disk('public')->get("{$themePath}/{$filePath}");

            if ($section && $element) {
                // Update specific section and element
                $fileContent = $this->updateSpecificElement($fileContent, $section, $element, $content);
            } else {
                // Update entire file
                $fileContent = $content;
            }

            Storage::disk('public')->put("{$themePath}/{$filePath}", $fileContent);

            return response()->json([
                'message' => 'Theme file updated successfully',
                'file_path' => $filePath
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update theme file: ' . $e->getMessage());
            return response()->json(
                ThemeCustomizationResource::error('Failed to update theme file'),
                500
            );
        }
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

        if (!$store || !$store->theme) {
            return response()->json([
                'success' => false,
                'message' => 'No active theme found for this store'
            ], 404);
        }

        $filePath = $request->input('file_path');

        if (!$filePath) {
            /**
             * If no file path provided, return list of available files
             */
            try {
                $themePath = "themes/user_{$user->id}/{$store->theme}";

                if (!Storage::disk('public')->exists($themePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Theme directory not found'
                    ], 404);
                }

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
            } catch (\Exception $e) {
                Log::error('Failed to list theme files: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to list theme files',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $themePath = "themes/user_{$user->id}/{$store->theme}";

            if (!Storage::disk('public')->exists("{$themePath}/{$filePath}")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Theme file not found',
                    'details' => [
                        'file_path' => $filePath,
                        'theme_path' => $themePath
                    ]
                ], 404);
            }

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
        } catch (\Exception $e) {
            Log::error('Failed to get theme file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get theme file',
                'error' => $e->getMessage()
            ], 500);
        }
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

        if (!$store || !$store->theme) {
            return response()->json(
                ThemeCustomizationResource::error('No active theme found for this store'),
                404
            );
        }

        try {
            $themePath = "themes/user_{$user->id}/{$store->theme}";

            if (!Storage::disk('public')->exists($themePath)) {
                return response()->json(
                    ThemeCustomizationResource::error('Theme directory not found'),
                    404
                );
            }

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
        } catch (\Exception $e) {
            Log::error('Failed to list HTML files: ' . $e->getMessage());
            return response()->json(
                ThemeCustomizationResource::error('Failed to list HTML files'),
                500
            );
        }
    }
}
