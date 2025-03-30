<?php

namespace App\Services\Services\Store;

use App\Models\Store;
use App\Services\Constructors\StoreConstructor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class StoreService implements StoreConstructor
{
    /**
     * Create store
     *
     * @param [type] $user
     * @return Store
     */
    public function createStore($user) : Store
    {
        $storeName = "Store of " . $user->username;
        $domain = Str::slug($user->username) . ".wee-build.com";

        if (app()->environment('local')) {
            $domain = Str::slug($user->username) . ".localhost";
        }

        $store = Store::create([
            'user_id' => $user->id,
            'name' => $storeName,
            'domain' => $domain,
            'is_active' => true,
        ]);

        $this->configureDomain($store);

        return $store;
    }

    /**
     * Configure domain for the store
     *
     * @param Store $store
     * @return void
     */
    protected function configureDomain(Store $store) : void
    {
        if (app()->environment('local')) {
            Log::info("Store domain configured for local environment: {$store->domain}");
            return;
        }

        Log::info("Store domain configured for production: {$store->domain}");
    }

    /**
     * Activate or deactivate a store
     *
     * @param Store $store
     * @param bool $active
     * @return Store
     */
    public function setStoreStatus(Store $store, bool $active = true) : Store
    {
        $store->update([
            'is_active' => $active,
        ]);

        return $store;
    }

    /**
     * Show store
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->load('store')->store;

        return response()->json([
            'user' => $user,
            'store' => new \App\Http\Resources\StoreResource($store)
        ]);
    }

    /**
     * Apply theme to store
     *
     * @param Request $request
     * @param string $themeName
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyTheme(Request $request, string $themeName) : JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found for this user'
            ], 404);
        }

        $themeData = $request->input('theme_data');

        $this->saveThemeToStorage($user->id, $themeName, $themeData);

        $store->update([
            'theme' => $themeName,
            'theme_applied_at' => now(),
            'theme_storage_path' => $this->getThemeStoragePath($user->id, $themeName)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Theme applied and saved successfully',
            'store' => $store
        ]);
    }

    /**
     * Save theme to local storage
     *
     * @param int $userId
     * @param string $themeName
     * @param array|null $themeData
     * @return bool
     */
    public function saveThemeToStorage(int $userId, string $themeName, $themeData = null) : bool
    {
        $storagePath = $this->getThemeStoragePath($userId, $themeName);

        if (!Storage::exists(dirname($storagePath))) {
            Storage::makeDirectory(dirname($storagePath), 0755, true);
        }

        if ($themeData && isset($themeData['files'])) {
            $success = true;

            Storage::put($storagePath . '/theme-info.json', json_encode([
                'name' => $themeName,
                'installed_at' => now()->toDateTimeString(),
            ]));

            foreach ($themeData['files'] as $file) {
                if ($file['type'] === 'file') {
                    $fileContent = $this->downloadFileContent($file['download_url']);
                    if ($fileContent) {
                        $filePath = $storagePath . '/' . $file['name'];
                        $result = Storage::put($filePath, $fileContent);
                        $success = $success && $result;
                    }
                } elseif ($file['type'] === 'dir') {
                    Storage::makeDirectory($storagePath . '/' . $file['name'], 0755, true);

                    $this->downloadDirectory($userId, $themeName, $file['path']);
                }
            }

            return $success;
        }

        return Storage::put($storagePath . '/index.html', '<html><body><h1>Theme: ' . $themeName . '</h1><p>Installed at: ' . now()->toDateTimeString() . '</p></body></html>');
    }

    /**
     * Download file content from GitHub
     *
     * @param string $url
     * @return string|null
     */
    protected function downloadFileContent(string $url) : ?string
    {
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Exception $e) {
            Log::error('Failed to download theme file: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Download a directory and its contents recursively
     *
     * @param int $userId
     * @param string $themeName
     * @param string $path
     * @return bool
     */
    protected function downloadDirectory(int $userId, string $themeName, string $path) : bool
    {
        $storagePath = $this->getThemeStoragePath($userId, $themeName);
        $relativePath = str_replace($themeName . '/', '', $path);

        try {
            $response = Http::get("https://api.github.com/repos/zobirofkir/wee-build-themes/contents/{$path}");

            if ($response->successful()) {
                $contents = $response->json();

                foreach ($contents as $item) {
                    $itemPath = $storagePath . '/' . $relativePath . '/' . $item['name'];

                    if ($item['type'] === 'file') {
                        $fileContent = $this->downloadFileContent($item['download_url']);
                        if ($fileContent) {
                            Storage::put($itemPath, $fileContent);
                        }
                    } elseif ($item['type'] === 'dir') {
                        Storage::makeDirectory($itemPath, 0755, true);
                        $this->downloadDirectory($userId, $themeName, $item['path']);
                    }
                }

                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to download theme directory: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Get theme storage path
     *
     * @param int $userId
     * @param string $themeName
     * @return string
     */
    public function getThemeStoragePath(int $userId, string $themeName) : string
    {
        return "themes/user_{$userId}/{$themeName}";
    }

    /**
     * Get stored theme data
     *
     * @param int $userId
     * @param string $themeName
     * @return array|null
     */
    public function getStoredThemeData(int $userId, string $themeName) : ?array
    {
        $storagePath = $this->getThemeStoragePath($userId, $themeName);
        $infoPath = $storagePath . '/theme-info.json';

        if (Storage::exists($infoPath)) {
            $themeInfo = json_decode(Storage::get($infoPath), true);

            // Get list of all files in the theme directory
            $files = $this->getThemeFiles($storagePath);

            return [
                'name' => $themeName,
                'installed_at' => $themeInfo['installed_at'] ?? now()->toDateTimeString(),
                'files' => $files,
                'preview_url' => url("themes/user_{$userId}/{$themeName}/index.html")
            ];
        }

        return null;
    }

    /**
     * Get all files in a theme directory
     *
     * @param string $path
     * @return array
     */
    protected function getThemeFiles(string $path) : array
    {
        $files = [];

        if (!Storage::exists($path)) {
            return $files;
        }

        $allFiles = Storage::allFiles($path);

        foreach ($allFiles as $file) {
            if (basename($file) === 'theme-info.json') {
                continue;
            }

            $relativePath = str_replace($path . '/', '', $file);
            $files[] = [
                'name' => basename($file),
                'path' => $relativePath,
                'full_path' => $file,
                'size' => Storage::size($file),
                'type' => 'file'
            ];
        }

        return $files;
    }

    /**
     * List all stored themes for a user
     *
     * @param int $userId
     * @return array
     */
    public function listStoredThemes(int $userId) : array
    {
        $basePath = "themes/user_{$userId}";

        if (!Storage::exists($basePath)) {
            return [];
        }

        $directories = Storage::directories($basePath);
        $themes = [];

        foreach ($directories as $directory) {
            $themeName = basename($directory);
            $themeData = $this->getStoredThemeData($userId, $themeName);

            if ($themeData) {
                $themes[] = [
                    'name' => $themeName,
                    'path' => $directory,
                    'data' => $themeData,
                    'installed_at' => $themeData['installed_at'] ?? null
                ];
            }
        }

        return $themes;
    }
}
