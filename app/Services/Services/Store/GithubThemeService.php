<?php

namespace App\Services\Services\Store;

use App\Services\Constructors\GithubThemeConstructor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class GithubThemeService implements GithubThemeConstructor
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    protected $cacheTtl = 3600;

    public function index()
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

            // Store in cache
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
    public function generateTestUrl($themeName)
    {
        return "https://zobirofkir.github.io/wee-build-themes/{$themeName}";
    }

    /**
     * Get a specific theme for testing
     *
     * @param string $themeName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTestTheme($themeName)
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
    public function clearCache()
    {
        Cache::forget('github_themes_list');

        // Clear individual theme caches
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
}
