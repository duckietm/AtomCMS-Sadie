<?php

namespace App\Models\Game\Player;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    protected $table = 'player_badges';

    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'badge_id',
        'slot',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

    public function getBadgeCodeAttribute(): ?string
    {
        return $this->badge->code ?? null;
    }
}
