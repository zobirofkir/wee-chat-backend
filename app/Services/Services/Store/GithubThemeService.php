<?php

namespace App\Services\Services\Store;

use App\Services\Constructors\GithubThemeConstructor;
use App\Services\Facades\StoreFacade;
use App\Http\Resources\GithubThemeResource;
use App\Services\Services\Store\Traits\GithubThemeServiceTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GithubThemeService implements GithubThemeConstructor
{
    /**
     * Use the GithubThemeServiceTrait
     */
    use GithubThemeServiceTrait;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $themes = Cache::get($this->cacheKey) ?? $this->fetchAndCacheThemes();

        return response()->json([
            'themes' => GithubThemeResource::collection($themes),
            'source' => Cache::has($this->cacheKey) ? 'cache' : 'api'
        ]);
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
                'theme' => GithubThemeResource::make(Cache::get($cacheKey)),
                'source' => 'cache'
            ]);
        }

            $response = Http::withHeaders($this->getGithubHeaders())
                ->get("https://api.github.com/repos/zobirofkir/wee-build-themes/contents/{$themeName}");

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
                    'theme' => GithubThemeResource::make($themeData),
                    'source' => 'api'
                ]);
            }

            $errorMessage = 'Unable to fetch theme details';
            if ($response->json('message')) {
                $errorMessage = $response->json('message');
            }

            Log::error('GitHub API error when fetching theme: ' . $errorMessage, [
                'theme' => $themeName,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'status_code' => $response->status()
            ], $response->status());
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

        if ($store->theme && $store->theme !== $themeName) {
            $oldThemePath = storage_path("app/public/themes/user_{$user->id}/{$store->theme}");
            if (is_dir($oldThemePath)) {
                $this->removeDirectory($oldThemePath);
            }
        }

        $cloneResult = $this->cloneAndExtractTheme($themeName, $user->id);

        if (!$cloneResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $cloneResult['message'] ?? 'Failed to download theme'
            ], 500);
        }

        $store->update([
            'theme' => $themeName,
            'theme_applied_at' => now(),
            'theme_storage_path' => $cloneResult['path']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Theme applied and saved successfully',
            'store' => GithubThemeResource::make($store->toArray()),
            'theme_details' => GithubThemeResource::make([
                'name' => $themeName,
                'preview_url' => url("themes/user_{$user->id}/{$themeName}/index.html")
            ])
        ]);
    }
}
