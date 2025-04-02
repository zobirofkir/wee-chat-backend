<?php

namespace App\Services\Constructors\Store;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
     * Serve store theme
     *
     * @param Request $request
     * @param string $domain
     * @param string|null $path
     * @return Response
     */
    public function serveStoreTheme(Request $request, string $domain, string $path = null) : Response;

    /**
     * Serve theme file
     *
     * @param Request $request
     * @param integer $userId
     * @param string $themeName
     * @param string $filePath
     * @return Response
     */
    public function serveThemeFile(Request $request, int $userId, string $themeName, string $filePath = 'index.html') : Response;
}
