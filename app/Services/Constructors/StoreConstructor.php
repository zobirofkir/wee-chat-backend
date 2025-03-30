<?php

namespace App\Services\Constructors;

use App\Models\Store;
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
    public function createStore($user) : Store;

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
