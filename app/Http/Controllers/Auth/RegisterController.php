<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Services\Facades\Auth\RegisterFacade;


class RegisterController extends Controller
{
    /**
     * Create new user
     *
     * @param RegisterRequest $request
     * @return RegisterResource
     */
    public function register(RegisterRequest $request) : RegisterResource
    {
        return RegisterFacade::register($request);
    }
}
