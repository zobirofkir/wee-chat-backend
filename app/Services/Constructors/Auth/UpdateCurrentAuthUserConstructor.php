<?php

namespace App\Services\Constructors\Auth;

use App\Http\Requests\UpdateCurrentAuthUserRequest;
use App\Http\Resources\UpdateCurrentAuthUserResource;
use App\Models\User;

interface UpdateCurrentAuthUserConstructor
{
    /**
     * Update Current Authenticated User
     *
     * @param UpdateCurrentAuthUserRequest $request
     * @return UpdateCurrentAuthUserResource
     */
    public function update(UpdateCurrentAuthUserRequest $request, User $user) : UpdateCurrentAuthUserResource;
}
