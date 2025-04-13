<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Cart;

class UserObserver
{
    public function created(User $user): void
    {
        Cart::create([
            'user_id' => $user->id,
        ]);
    }
} 