<?php

namespace App\Services\Services\Store;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Models\User;
use App\Services\Constructors\Store\StoreConstructor;
use App\Services\Services\Store\Traits\StoreServiceTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;

class StoreService implements StoreConstructor
{
    /**
     * Use store service trait
     */
    use StoreServiceTrait;

    /**
     * Create store
     *
     * @param [type] $user
     * @return Store
     */
    public function createStore(User $user) : Store
    {
        $store = Store::create([
            'user_id' => $user->id,
            'name' => $this->generateStoreName($user),
            'domain' => $this->generateDomain($user)
        ]);

        $this->configureDomain($store);

        return $store;
    }

    /**
     * Show store
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request) : JsonResponse
    {
        $user = $request->user();
        $store = $user->load('store')->store;

        return response()->json([
            'user' => $user,
            'store' => StoreResource::make($store)
        ]);
    }

    /**
     * Serve theme file
     *
     * @param Request $request
     * @param integer $userId
     * @param string $themeName
     * @param string $filePath
     * @return Response
     */
    public function serveThemeFile(Request $request, int $userId, string $themeName, string $filePath = 'index.html') : Response
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
     * Serve store theme
     *
     * @param Request $request
     * @param string $domain
     * @param string|null $path
     * @return Response
     */
    public function serveStoreTheme(Request $request, string $domain, string $path = null) : Response
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
