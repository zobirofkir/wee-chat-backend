<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GithubThemeController extends Controller
{
    public function index()
    {
        try {
            // GitHub API endpoint for the repository contents
            $response = Http::get('https://api.github.com/repos/zobirofkir/wee-build-themes/contents');

            if ($response->successful()) {
                $contents = $response->json();

                // Filter and format the themes
                $themes = collect($contents)
                    ->filter(function ($item) {
                        return $item['type'] === 'dir'; // Only get directories
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching themes: ' . $e->getMessage()
            ], 500);
        }
    }
}
