<?php

namespace App\Services\Services\Store;

use App\Services\Constructors\GithubThemeConstructor;
use App\Services\Facades\StoreFacade;
use App\Http\Resources\GithubThemeResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GithubThemeService implements GithubThemeConstructor
{
    /**
     * Cache key for the themes list
     *
     * @var string
     */
    private string $cacheKey = 'github_themes_list';

    /**
     * Cache TTL in seconds (1 hour)
     */
    protected $cacheTtl = 3600;

    /**
     * Get GitHub API headers with authentication if available
     *
     * @return array
     */
    protected function getGithubHeaders(): array
    {
        $headers = [];
        if (config('services.github.token')) {
            $headers['Authorization'] = 'token ' . config('services.github.token');
        }
        return $headers;
    }

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
     * Fetch themes from GitHub and cache themes list
     */
    private function fetchAndCacheThemes()
    {
        $response = Http::withHeaders($this->getGithubHeaders())
            ->get('https://api.github.com/repos/zobirofkir/wee-build-themes/contents');

        if (!$response->successful()) {
            return $this->handleApiError($response);
        }

        $themes = collect($response->json())
            ->where('type', 'dir')
            ->map(fn($item) => [
                'id' => $item['sha'],
                'name' => $item['name'],
                'path' => $item['path'],
                'url' => $item['html_url'],
                'test_url' => $this->generateTestUrl($item['name']),
                'type' => 'free',
                'category' => 'e-commerce'
            ])
            ->values()
            ->toArray();

        Cache::put($this->cacheKey, $themes, $this->cacheTtl);

        return $themes;
    }

    /**
     * Handle API error responses
     *
     * @param [type] $response
     * @return JsonResponse
     */
    private function handleApiError($response) : JsonResponse
    {
        return response()->json([
            'message' => $response->json('message', 'Unable to fetch themes'),
            'status_code' => $response->status()
        ], $response->status());
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

            Log::error('GitHub API error in getThemeDetails: ' . $errorMessage, [
                'theme' => $themeName,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'status_code' => $response->status()
            ];
    }

    /**
     * Clone the theme repository and extract specific theme
     *
     * @param string $themeName
     * @param int $userId
     * @return array
     */
    private function cloneAndExtractTheme(string $themeName, int $userId): array
    {
            $tempDir = storage_path("app/temp/themes/{$userId}/{$themeName}");
            $targetDir = storage_path("app/public/themes/user_{$userId}/{$themeName}");

            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $repoUrl = 'https://github.com/zobirofkir/wee-build-themes.git';
            $command = "git clone {$repoUrl} {$tempDir}/repo";

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('Failed to clone repository');
            }

            $themeSource = "{$tempDir}/repo/{$themeName}";
            if (!is_dir($themeSource)) {
                throw new \Exception("Theme {$themeName} not found in repository");
            }

            $this->copyDirectory($themeSource, $targetDir);

            $this->removeDirectory($tempDir);

            return [
                'success' => true,
                'path' => $targetDir
            ];
    }

    /**
     * Copy directory recursively
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $files = scandir($source);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $file;
            $destinationPath = $destination . '/' . $file;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destinationPath);
            } else {
                copy($sourcePath, $destinationPath);
            }
        }
    }

    /**
     * Remove directory recursively
     *
     * @param string $directory
     * @return void
     */
    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $directory . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
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
