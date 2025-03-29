<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Facades\StoreFacade;

class UserObserver
{
    public function created(User $user)
    {
        StoreFacade::createStore($user);
    }
}
