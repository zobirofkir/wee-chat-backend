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
        return RegisterResource::make(
            User::create(
                $request->validated()
            )
        );
    }
}
