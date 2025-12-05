<?php

namespace App\Observers;

use App\Models\User;
use App\Models\User\PlayerSubscription;

class UserObserver
{
    public function created(User $user): void
    {
        // PlayerData is now created in CreateNewUser, so DON'T touch it here.

        // HC subscription on register
        if ((setting('give_hc_on_register') ?: '0') == '1') {
            PlayerSubscription::create([
                'player_id' => $user->id,
                'subscription_id' => 1, // Habbo Club
                'created_at' => now(),
                'expires_at' => now()->addDays(
                    (int) (setting('hc_on_register_duration') ?: 0),
                ),
            ]);
        }
    }
}
