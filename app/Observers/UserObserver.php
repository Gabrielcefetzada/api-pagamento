<?php

namespace App\Observers;

use App\Models\User as ModelsUser;
use App\Models\Wallet;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(ModelsUser $user): void
    {
        Wallet::create(
            [
                'user_id' => $user->id,
            ]
        );
    }
}
