<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PlayerData extends Model
{
    protected $table = 'player_data';

    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'home_room_id',
        'credit_balance',
        'pixel_balance',
        'seasonal_balance',
        'gotw_points',
        'respect_points',
        'respect_points_pet',
        'achievement_score',
        'allow_friend_requests',
        'is_online',
        'last_online',
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
