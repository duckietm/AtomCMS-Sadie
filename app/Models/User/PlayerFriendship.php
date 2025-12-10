<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerFriendship extends Model
{
    protected $table = 'player_friendships';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function originPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'origin_player_id');
    }

    public function targetPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_player_id');
    }
}
