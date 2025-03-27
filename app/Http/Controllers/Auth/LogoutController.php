<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Facades\Auth\LogoutFacade;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Logout the user
     *
     * @return boolean
     */
    public function logout() : bool
    {
        return LogoutFacade::logout();
    }
}
