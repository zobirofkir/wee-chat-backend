<?php

namespace App\Services\Services\Auth;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Services\Constructors\Auth\AuthConstructor;
use Illuminate\Support\Facades\Auth;

class AuthService implements AuthConstructor
{
    /**
     * Show current authenticated user
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
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
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        $user->delete();
        return true;
    }
}
