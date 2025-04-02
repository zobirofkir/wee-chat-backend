<?php

namespace App\Services\Services\Store;

use App\Services\Constructors\Store\GithubThemeConstructor;
use App\Services\Facades\StoreFacade;
use App\Http\Resources\GithubThemeResource;
use App\Services\Services\Store\Traits\GithubThemeServiceTrait;
use App\Services\Services\Store\Traits\StoreServiceTrait;
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
    public function getTestTheme($themeName): JsonResponse
    {
        $cacheKey = "github_theme_{$themeName}";

        if ($theme = Cache::get($cacheKey)) {
            return $this->jsonResponse(true, $theme, 'cache');
        }

        $response = Http::withHeaders($this->getGithubHeaders())
            ->get("https://api.github.com/repos/zobirofkir/wee-build-themes/contents/{$themeName}");

        if (!$response->successful()) {
            return $this->handleApiError($response, "فشل في جلب تفاصيل الثيم {$themeName}");
        }

        $themeData = [
            'name' => $themeName,
            'files' => $response->json(),
            'preview_url' => $this->generateTestUrl($themeName)
        ];

        Cache::put($cacheKey, $themeData, $this->cacheTtl);

        return $this->jsonResponse(true, $themeData, 'api');
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
    public function applyTheme(Request $request, string $themeName): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->jsonError('User not authenticated', 401);
        }

        $store = $user->store;

        if (!$store) {
            return $this->jsonError('Store not found for this user', 404);
        }

        $this->removeOldTheme($user->id, $store->theme);

        $cloneResult = $this->cloneAndExtractTheme($themeName, $user->id);

        if (!$cloneResult['success']) {
            return $this->jsonError($cloneResult['message'] ?? 'Failed to download theme', 500);
        }

        $store->update([
            'theme' => $themeName,
            'theme_applied_at' => now(),
            'theme_storage_path' => $cloneResult['path']
        ]);

        return $this->jsonSuccess('Theme applied and saved successfully', [
            'store' => GithubThemeResource::make($store->toArray()),
            'theme_details' => [
                'name' => $themeName,
                'preview_url' => url("themes/user_{$user->id}/{$themeName}/index.html")
            ]
        ]);
    }
}
