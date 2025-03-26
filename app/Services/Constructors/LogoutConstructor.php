<?php

namespace App\Services\Constructors;

interface LogoutConstructor
{
    /**
     * Logout
     *
     * @return bool
     */
    public function logout() : bool;
}
