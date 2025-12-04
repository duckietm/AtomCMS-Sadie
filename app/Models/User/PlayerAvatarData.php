<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PlayerAvatarData extends Model
{
    protected $table = 'player_avatar_data';

    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'figure_code',
        'motto',
        'gender',
        'chat_bubble_id',
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
