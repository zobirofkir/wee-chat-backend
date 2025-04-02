<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Facades\Store\StoreFacade;

class UserObserver
{
    public function created(User $user)
    {
        StoreFacade::createStore($user);
    }
}
