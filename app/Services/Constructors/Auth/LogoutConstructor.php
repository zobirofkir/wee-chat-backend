<?php

namespace App\Services\Constructors\Auth;

interface LogoutConstructor
{
    /**
     * Logout
     *
     * @return bool
     */
    public function logout() : bool;
}
