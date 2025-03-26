<?php

namespace App\Services\Services;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use App\Services\Constructors\LoginConstructor;
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
        $request->validated();

        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return abort(401, "Email ou mot de passe incorrects");
        }

        return $user;
    }
}
