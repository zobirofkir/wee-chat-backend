<?php

namespace App\Services\Constructors\Auth;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;

interface RegisterConstructor
{
    /**
     * Store a newly created resource in storage.
     *
     * @param RegisterRequest $request
     * @return RegisterResource
     */
    public function register(RegisterRequest $request) : RegisterResource;
}
