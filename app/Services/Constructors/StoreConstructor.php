<?php

namespace App\Services\Constructors;

use App\Models\Store;
use Illuminate\Http\Request;

interface StoreConstructor
{
    /**
     * Create store
     *
     * @param [type] $user
     * @return Store
     */
    public function createStore($user) : Store;

    /**
     * Show store
     *
     * @param Request $request
     * @return void
     */
    public function show(Request $request);
}
