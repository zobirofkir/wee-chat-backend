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
        $path = "themes/user_{$userId}/{$themeName}/{$filePath}";

        if (!Storage::exists($path)) {
            abort(404);
        }

        $file = Storage::get($path);
        $type = Storage::mimeType($path);

        return response($file)->header('Content-Type', $type);
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
        $store = Store::where('domain', $domain)->first();

        if (!$store || !$store->is_active || !$store->theme) {
            abort(404);
        }

        $filePath = $path ?: 'index.html';

        $storagePath = "themes/user_{$store->user_id}/{$store->theme}/{$filePath}";

        if (!Storage::exists($storagePath)) {
            abort(404);
        }

        $file = Storage::get($storagePath);
        $type = Storage::mimeType($storagePath);

        return response($file)->header('Content-Type', $type);
    }
}
