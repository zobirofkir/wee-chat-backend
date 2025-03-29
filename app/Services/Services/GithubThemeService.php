<?php

namespace App\Services\Services;

use App\Services\Constructors\GithubThemeConstructor;
use Illuminate\Support\Facades\Http;

class GithubThemeService implements GithubThemeConstructor
{
    public function index()
    {
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
                        'test_url' => $this->generateTestUrl($item['name'])
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'themes' => $themes
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
        return url("/theme-preview/{$themeName}");
    }

    /**
     * Get a specific theme for testing
     *
     * @param string $themeName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTestTheme($themeName)
    {
        $response = Http::get("https://api.github.com/repos/zobirofkir/wee-build-themes/contents/{$themeName}");

        if ($response->successful()) {
            $contents = $response->json();

            return response()->json([
                'success' => true,
                'theme' => [
                    'name' => $themeName,
                    'files' => $contents,
                    'preview_url' => $this->generateTestUrl($themeName)
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch theme details'
        ], 404);
    }
}
