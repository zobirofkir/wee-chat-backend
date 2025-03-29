<?php

namespace App\Http\Controllers;

use App\Services\Facades\StoreFacade;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Show store
     *
     * @param Request $request
     */
    public function show(Request $request)
    {
        return StoreFacade::show($request);
    }
}
