<?php

namespace App\Services\Services\Auth;

use App\Http\Requests\UpdateCurrentAuthUserRequest;
use App\Http\Resources\UpdateCurrentAuthUserResource;
use App\Models\User;
use App\Services\Constructors\Auth\UpdateCurrentAuthUserConstructor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateCurrentAuthUserService implements UpdateCurrentAuthUserConstructor
{
    /**
     * Update Current Authenticated User
     *
     * @param UpdateCurrentAuthUserRequest $request
     * @return User
     */
    public function update(UpdateCurrentAuthUserRequest $request, User $user): User
    {
        $data = $request->validated();

        if (isset($data['avatar']) && $data['avatar']) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $data['avatar']->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->refresh();
    }
}
