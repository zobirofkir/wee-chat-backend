<?php

namespace App\Services\Services\Auth;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Models\User;
use App\Services\Constructors\Auth\RegisterConstructor;

class RegisterService implements RegisterConstructor
{
    /**
     * Create new user
     *
     * @param RegisterRequest $request
     * @return RegisterResource
     */
    public function register(RegisterRequest $request): RegisterResource
    {
        $validatedData = $request->validated();
        if ($validatedData['avatar'] ?? null) {
            $path = $validatedData['avatar']->store('avatars', 'public');
            $validatedData['avatar'] = $path;
        }
        return RegisterResource::make(
            User::create($validatedData)
        );
    }
}
