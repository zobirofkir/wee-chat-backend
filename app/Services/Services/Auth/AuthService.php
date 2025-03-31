<?php

namespace App\Services\Services\Auth;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Services\Constructors\Auth\AuthConstructor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthService implements AuthConstructor
{
    /**
     * Show current authenticated user
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user, Request $request): UserResource
    {
        $user = $request->user();
        $store = $user->load('store')->store;

        return UserResource::make($user);
    }

    /**
     * delete current authenticated user
     *
     * @param User $user
     * @return boolean
     */
    public function delete(User $user): bool
    {
        $user->delete();
        return true;
    }
}
