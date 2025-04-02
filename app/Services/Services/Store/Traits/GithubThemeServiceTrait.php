<?php

namespace App\Services\Services\Store\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

trait GithubThemeServiceTrait
{
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
}