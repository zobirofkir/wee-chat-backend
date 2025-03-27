<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Facades\Auth\AuthFacade;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show current authenticated user
     *
     * @return mixed
     */
    public function show()
    {
        return AuthFacade::show(auth()->user());
    }

    /**
     * Delete current authenticated user
     *
     * @return mixed
     */
    public function delete()
    {
        return AuthFacade::delete(auth()->user());
    }
}
