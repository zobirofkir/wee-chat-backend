<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Services\Facades\Store\StoreFacade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /**
     * Show store
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request) : JsonResponse
    {
        return StoreFacade::show($request);
    }

    /**
     * Serve theme files
     *
     * @param Request $request
     * @param int $userId
     * @param string $themeName
     * @param string $filePath
     * @return \Illuminate\Http\Response
     */
    public function serveThemeFile(Request $request, int $userId, string $themeName, string $filePath = 'index.html')
    {
        return StoreFacade::serveThemeFile($request, $userId, $themeName, $filePath);
    }

    /**
     * Serve theme files from the store's custom domain
     *
     * @param Request $request
     * @param string $domain
     * @param string|null $path
     * @return \Illuminate\Http\Response
     */
    public function serveStoreTheme(Request $request, string $domain, string $path = null)
    {
        return StoreFacade::serveStoreTheme($request, $domain, $path);
    }
}
