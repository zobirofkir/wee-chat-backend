<?php

namespace App\Services\Services;

use App\Models\Store;
use App\Services\Constructors\StoreConstructor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
        $storeName = "Store of " . $user->name;
        $domain = Str::slug($user->name) . ".wee-build.com";

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
            'user' => $request->user()->load('stores'),
        ]);
    }
}
