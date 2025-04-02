<?php

namespace App\Services\Services\Store\Traits;

use App\Http\Resources\GithubThemeResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

trait GithubThemeServiceTrait
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
    private $cacheTtl = 3600;

    /**
     * Get theme storage path
     *
     * @param int $userId
     * @param string $themeName
     * @return string
     */
    private function getThemeStoragePath(int $userId, string $themeName) : string
    {
        return "themes/user_{$userId}/{$themeName}";
    }

    /**
     * Get GitHub API headers with authentication if available
     *
     * @return array
     */
    private function getGithubHeaders(): array
    {
        $headers = [];
        if (config('services.github.token')) {
            $headers['Authorization'] = 'token ' . config('services.github.token');
        }
        return $headers;
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
     * Json response
     *
     * @param boolean $success
     * @param array $data
     * @param string $source
     * @return JsonResponse
     */
    private function jsonResponse(bool $success, array $data, string $source): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'theme' => GithubThemeResource::make($data),
            'source' => $source
        ]);
    }

    /**
     * Remove old theme
     *
     * @param [type] $store
     * @param integer $userId
     * @return void
     */
    private function removeOldTheme(int $userId, string $themeName) : bool
    {
        $storagePath = $this->getThemeStoragePath($userId, $themeName);

        if (Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->deleteDirectory($storagePath);
        }

        return false;
    }

    /**
     * Json error
     *
     * @param string $message
     * @param integer $status
     * @return JsonResponse
     */
    private function jsonError(string $message, int $status): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }

    /**
     * Json success
     *
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    private function jsonSuccess(string $message, array $data = []): JsonResponse
    {
        return response()->json(array_merge(['success' => true, 'message' => $message], $data));
    }
}
