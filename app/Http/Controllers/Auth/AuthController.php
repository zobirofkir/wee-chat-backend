<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Facades\Auth\AuthFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show current authenticated user
     *
     * @return UserResource
     */
    public function show() : UserResource
    {
        return AuthFacade::show(Auth::user());
    }

    /**
     * Delete current authenticated user
     *
     * @return bool
     */
    public function delete() : bool
    {
        return AuthFacade::delete(Auth::user());
    }
}
