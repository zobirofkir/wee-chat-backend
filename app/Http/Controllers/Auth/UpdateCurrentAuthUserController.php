<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCurrentAuthUserRequest;
use App\Models\User;
use App\Services\Facades\Auth\UpdateCurrentAuthUserFacade;
use Illuminate\Http\Request;

class UpdateCurrentAuthUserController extends Controller
{
    public function update(UpdateCurrentAuthUserRequest $request, User $user)
    {
        return UpdateCurrentAuthUserFacade::update($request, $user);
    }
}
