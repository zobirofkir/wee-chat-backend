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
                        'name' => $item['name'],
                        'path' => $item['path'],
                        'url' => $item['html_url']
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
}
