<?php

namespace App\Services\Services\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use App\Services\Constructors\Auth\LoginConstructor;
use Illuminate\Support\Facades\Hash;

class LoginService implements LoginConstructor
{
    /**
     * Méthode de connexion
     *
     * @param LoginRequest $request
     * @return User
     */
    public function login(LoginRequest $request): User
    {
        $validated = $request->validated();

        $user = User::where("email", $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return abort(401, "Email ou mot de passe incorrects");
        }

        return $user;
    }
}
