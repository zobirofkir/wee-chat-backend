<?php

namespace App\Http\Controllers;

use App\Services\Facades\StoreFacade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
}
