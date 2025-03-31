<?php

namespace App\Services\Constructors\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

interface AuthConstructor
{
    /**
     * Show authenticated user
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user, Request $request) : UserResource;

    /**
     * Delete Current Authenticated User Account
     *
     * @param User $user
     * @return boolean
     */
    public function delete(User $user) : bool;
}
