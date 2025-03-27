<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCurrentAuthUserRequest;
use App\Http\Resources\UpdateCurrentAuthUserResource;
use App\Models\User;
use App\Services\Facades\Auth\UpdateCurrentAuthUserFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateCurrentAuthUserController extends Controller
{
    /**
     * Update Current Authenticated User
     *
     * @param UpdateCurrentAuthUserRequest $request
     * @return UpdateCurrentAuthUserResource
     */
    public function update(UpdateCurrentAuthUserRequest $request) : UpdateCurrentAuthUserResource
    {
        $user = Auth::user();
        $user = UpdateCurrentAuthUserFacade::update($request, $user);

        return UpdateCurrentAuthUserResource::make($user);
    }
}
