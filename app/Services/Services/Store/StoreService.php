<?php

namespace App\Services\Services\Store;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Models\User;
use App\Services\Constructors\StoreConstructor;
use App\Services\Services\Store\Traits\StoreServiceTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class StoreService implements StoreConstructor
{
    /**
     * Use store service trait
     */
    use StoreServiceTrait;

    /**
     * Create store
     *
     * @param [type] $user
     * @return Store
     */
    public function createStore(User $user) : Store
    {
        $store = Store::create([
            'user_id' => $user->id,
            'name' => $this->generateStoreName($user),
            'domain' => $this->generateDomain($user)
        ]);

        $this->configureDomain($store);

        return $store;
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
            'store' => StoreResource::make($store)
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

        if ($store->theme && $store->theme !== $themeName) {
            $this->removeOldTheme($user->id, $store->theme);
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

        if (!Storage::disk('public')->exists(dirname($storagePath))) {
            Storage::disk('public')->makeDirectory(dirname($storagePath), 0755, true);
        }

        if ($themeData && isset($themeData['files'])) {
            $success = true;

            Storage::disk('public')->put($storagePath . '/theme-info.json', json_encode([
                'name' => $themeName,
                'installed_at' => now()->toDateTimeString(),
            ]));

            foreach ($themeData['files'] as $file) {
                if ($file['type'] === 'file') {
                    $fileContent = $this->downloadFileContent($file['download_url']);
                    if ($fileContent) {
                        $filePath = $storagePath . '/' . $file['name'];
                        $result = Storage::disk('public')->put($filePath, $fileContent);
                        $success = $success && $result;
                    }
                } elseif ($file['type'] === 'dir') {
                    Storage::disk('public')->makeDirectory($storagePath . '/' . $file['name'], 0755, true);

                    $this->downloadDirectory($userId, $themeName, $file['path']);
                }
            }

            return $success;
        }

        return Storage::disk('public')->put($storagePath . '/index.html', '<html><body><h1>Theme: ' . $themeName . '</h1><p>Installed at: ' . now()->toDateTimeString() . '</p></body></html>');
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

        if (Storage::disk('public')->exists($infoPath)) {
            $themeInfo = json_decode(Storage::disk('public')->get($infoPath), true);

            $files = $this->getThemeFiles($storagePath);

            return [
                'name' => $themeName,
                'installed_at' => $themeInfo['installed_at'] ?? now()->toDateTimeString(),
                'files' => $files,
                'preview_url' => asset("storage/{$storagePath}/index.html")
            ];
        }

        return null;
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

        if (!Storage::disk('public')->exists($basePath)) {
            return [];
        }

        $directories = Storage::disk('public')->directories($basePath);
        $themes = [];

        foreach ($directories as $directory) {
            $themeName = basename($directory);
            $infoPath = $directory . '/theme-info.json';

            if (Storage::disk('public')->exists($infoPath)) {
                $themeInfo = json_decode(Storage::disk('public')->get($infoPath), true);

                $themes[] = [
                    'name' => $themeName,
                    'path' => $directory,
                    'installed_at' => $themeInfo['installed_at'] ?? null,
                    'preview_url' => asset("storage/{$directory}/index.html")
                ];
            }
        }

        return $themes;
    }
}
