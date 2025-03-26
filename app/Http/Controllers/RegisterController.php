<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Services\Facades\RegisterFacade;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Create new user
     *
     * @param RegisterRequest $request
     * @return RegisterResource
     */
    public function store(RegisterRequest $request) : RegisterResource
    {
        return RegisterFacade::store($request);
    }
}
