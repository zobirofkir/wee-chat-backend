<?php

namespace App\Services\Services\Store;

use App\Services\Constructors\GithubThemeConstructor;
use App\Services\Facades\StoreFacade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GithubThemeService implements GithubThemeConstructor
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    protected $cacheTtl = 3600;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $cacheKey = 'github_themes_list';

        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => true,
                'themes' => Cache::get($cacheKey),
                'source' => 'cache'
            ]);
        }

        $response = Http::get('https://api.github.com/repos/zobirofkir/wee-build-themes/contents');

        if ($response->successful()) {
            $contents = $response->json();

            $themes = collect($contents)
                ->filter(function ($item) {
                    return $item['type'] === 'dir';
                })
                ->map(function ($item) {
                    return [
                        'id' => $item['sha'],
                        'name' => $item['name'],
                        'path' => $item['path'],
                        'url' => $item['html_url'],
                        'test_url' => $this->generateTestUrl($item['name']),
                        'type' => 'free',
                        'category' => 'e-commerce'
                    ];
                })
                ->values();

            Cache::put($cacheKey, $themes, $this->cacheTtl);

            return response()->json([
                'success' => true,
                'themes' => $themes,
                'source' => 'api'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch themes'
        ], 500);
    }

    /**
     * Generate a test URL for a theme
     *
     * @param string $themeName
     * @return string
     */
    public function generateTestUrl($themeName) : string
    {
        return "https://zobirofkir.github.io/wee-build-themes/{$themeName}";
    }

    /**
     * Get a specific theme for testing
     *
     * @param string $themeName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTestTheme($themeName) : JsonResponse
    {
        $cacheKey = "github_theme_{$themeName}";

        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => true,
                'theme' => Cache::get($cacheKey),
                'source' => 'cache'
            ]);
        }

        $response = Http::get("https://api.github.com/repos/zobirofkir/wee-build-themes/contents/{$themeName}");

        if ($response->successful()) {
            $contents = $response->json();

            $themeData = [
                'name' => $themeName,
                'files' => $contents,
                'preview_url' => $this->generateTestUrl($themeName)
            ];

            Cache::put($cacheKey, $themeData, $this->cacheTtl);

            return response()->json([
                'success' => true,
                'theme' => $themeData,
                'source' => 'api'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch theme details'
        ], 404);
    }

    /**
     * Clear the cache for all themes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache() : JsonResponse
    {
        Cache::forget('github_themes_list');

        $keys = Redis::keys('laravel_cache:github_theme_*');
        foreach ($keys as $key) {
            $cacheKey = str_replace('laravel_cache:', '', $key);
            Cache::forget($cacheKey);
        }

        return response()->json([
            'success' => true,
            'message' => 'Theme cache cleared successfully'
        ]);
    }

    /**
     * Apply a theme to a user's store
     *
     * @param Request $request
     * @param string $themeName
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyTheme(Request $request, string $themeName) : JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $store = $user->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found for this user'
            ], 404);
        }

        $themeDetails = $this->getThemeDetails($themeName);

        if (!$themeDetails['success']) {
            return response()->json([
                'success' => false,
                'message' => $themeDetails['message'] ?? 'Theme not found'
            ], 404);
        }

        // Save theme data to local storage
        StoreFacade::saveThemeToStorage($user->id, $themeName, $themeDetails['theme']);

        $store->update([
            'theme' => $themeName,
            'theme_applied_at' => now(),
            'theme_data' => json_encode($themeDetails['theme']),
            'theme_storage_path' => StoreFacade::getThemeStoragePath($user->id, $themeName)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Theme applied and saved successfully',
            'store' => $store,
            'theme_details' => $themeDetails['theme']
        ]);
    }

    /**
     * Get theme details (private helper method)
     *
     * @param string $themeName
     * @return array
     */
    private function getThemeDetails(string $themeName)
    {
        $cacheKey = "github_theme_{$themeName}";

        if (Cache::has($cacheKey)) {
            return [
                'success' => true,
                'theme' => Cache::get($cacheKey),
                'source' => 'cache'
            ];
        }

        try {
            $headers = [];
            if (config('services.github.token')) {
                $headers['Authorization'] = 'token ' . config('services.github.token');
            }

            $response = Http::withHeaders($headers)
                ->get("https://api.github.com/repos/zobirofkir/wee-build-themes/contents/{$themeName}");

            if ($response->successful()) {
                $contents = $response->json();

                $themeData = [
                    'name' => $themeName,
                    'files' => $contents,
                    'preview_url' => $this->generateTestUrl($themeName)
                ];

                Cache::put($cacheKey, $themeData, $this->cacheTtl);

                return [
                    'success' => true,
                    'theme' => $themeData,
                    'source' => 'api'
                ];
            }

            $errorMessage = 'Unable to fetch theme details';
            if ($response->json('message')) {
                $errorMessage = $response->json('message');
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error connecting to GitHub API: ' . $e->getMessage()
            ];
        }
    }
}
