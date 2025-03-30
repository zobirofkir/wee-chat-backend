<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreResource;
use App\Services\Facades\StoreFacade;
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
}
