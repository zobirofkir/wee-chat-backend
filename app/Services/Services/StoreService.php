<?php

namespace App\Services\Services;

use App\Models\Store;
use App\Services\Constructors\StoreConstructor;
use Illuminate\Support\Str;

class StoreService implements StoreConstructor
{
    public function createStore($user)
    {
        $storeName = "Store of " . $user->name;
        $domain = Str::slug($user->name) . ".mystore.com";

        return Store::create([
            'user_id' => $user->id,
            'name' => $storeName,
            'domain' => $domain,
        ]);
    }
}
