<?php

namespace App\Http\Controllers;

use App\Services\Facades\LogoutFacade;
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
