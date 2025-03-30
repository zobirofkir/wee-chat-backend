<?php

namespace App\Services\Services\Store;

use App\Models\Store;
use App\Services\Constructors\StoreConstructor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
            \Log::info("Store domain configured for local environment: {$store->domain}");
            return;
        }

        \Log::info("Store domain configured for production: {$store->domain}");
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

        $store->update([
            'theme' => $themeName,
            'theme_applied_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Theme applied successfully',
            'store' => $store
        ]);
    }
}
