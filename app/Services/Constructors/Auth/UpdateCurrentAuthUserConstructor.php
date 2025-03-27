<?php

namespace App\Services\Constructors\Auth;

use App\Http\Requests\UpdateCurrentAuthUserRequest;
use App\Models\User;

interface UpdateCurrentAuthUserConstructor
{
    /**
     * Update Current Authenticated User
     *
     * @param UpdateCurrentAuthUserRequest $request
     * @return User
     */
    public function update(UpdateCurrentAuthUserRequest $request, User $user) : User;
}
