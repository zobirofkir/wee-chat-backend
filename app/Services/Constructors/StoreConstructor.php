<?php

namespace App\Services\Constructors;

use App\Models\Store;

interface StoreConstructor
{
    /**
     * Create store
     *
     * @param [type] $user
     * @return Store
     */
    public function createStore($user) : Store;
}
