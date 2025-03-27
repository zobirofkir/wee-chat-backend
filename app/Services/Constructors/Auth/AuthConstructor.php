<?php

namespace App\Services\Constructors\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;

interface AuthConstructor
{
    /**
     * Show authenticated user
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user) : UserResource;

    /**
     * Delete Current Authenticated User Account
     *
     * @param User $user
     * @return boolean
     */
    public function delete(User $user) : bool;
}
