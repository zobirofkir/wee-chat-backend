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

        return Store::create([
            'user_id' => $user->id,
            'name' => $storeName,
            'domain' => $domain,
        ]);
    }

    /**
     * Show store
     *
     * @param Request $request
     */
    public function show(Request $request)
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

        // Update store with theme information
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
