<?php

namespace App\Services\Services;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Models\User;
use App\Services\Constructors\RegisterConstructor;

class RegisterService implements RegisterConstructor
{
    public function store(RegisterRequest $request): RegisterResource
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
