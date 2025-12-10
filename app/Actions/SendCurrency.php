<?php

namespace App\Actions;

use App\Models\User;
use App\Services\RconService;

class SendCurrency
{
    public function __construct(protected RconService $rcon) {}

    public function execute(User $user, string $type, ?int $amount, bool $allowRcon = true): bool
    {
        if ($amount === null || $amount === 0) {
            return false;
        }

        $useRcon = false;

        if ($allowRcon) {
            try {
                $useRcon = $this->rcon->isConnected();
            } catch (\Throwable $e) {
                $useRcon = false;
            }
        }

        if ($useRcon) {
            return match ($type) {
                'credits'  => (bool) $this->rcon->giveCredits($user, $amount),
                'duckets'  => (bool) $this->rcon->giveDuckets($user, $amount),
                'diamonds' => (bool) $this->rcon->giveDiamonds($user, $amount),
                'points'   => (bool) $this->rcon->giveGotw($user, $amount),
                default    => false,
            };
        }

        $data = $user->data;

        if (! $data) {
            return false;
        }

        return match ($type) {
            'credits'  => (bool) $data->increment('credit_balance', $amount),
            'duckets'  => (bool) $data->increment('pixel_balance', $amount),
            'diamonds' => (bool) $data->increment('seasonal_balance', $amount),
            'points'   => (bool) $data->increment('gotw_points', $amount),
            default    => false,
        };
    }
}
