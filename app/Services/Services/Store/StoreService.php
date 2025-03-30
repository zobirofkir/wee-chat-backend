<?php

namespace App\Services\Services\Store;

use App\Models\Store;
use App\Services\Constructors\StoreConstructor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        return response()->json([
            'user' => $request->user()->load('store'),
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

        // Get theme details from the request if available
        $themeData = $request->input('theme_data');

        // Save theme locally
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

        // Create directory if it doesn't exist
        if (!Storage::exists(dirname($storagePath))) {
            Storage::makeDirectory(dirname($storagePath), 0755, true);
        }

        // If theme data is provided, save it
        if ($themeData) {
            return Storage::put($storagePath . '/theme.json', json_encode($themeData));
        }

        // If no theme data, create an empty theme file
        return Storage::put($storagePath . '/theme.json', json_encode([
            'name' => $themeName,
            'installed_at' => now()->toDateTimeString(),
        ]));
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
        $storagePath = $this->getThemeStoragePath($userId, $themeName) . '/theme.json';

        if (Storage::exists($storagePath)) {
            $themeData = json_decode(Storage::get($storagePath), true);
            return $themeData;
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
