<?php

namespace App\Services\Constructors\Store;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface StoreConstructor
{
    /**
     * Create store
     *
     * @param [type] $user
     * @return Store
     */
    public function createStore(User $user) : Store;

    /**
     * Show store
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request) : JsonResponse;

    /**
     * Apply theme to store
     *
     * @param Request $request
     * @param string $themeName
     * @return JsonResponse
     */
    public function applyTheme(Request $request, string $themeName) : JsonResponse;
}
