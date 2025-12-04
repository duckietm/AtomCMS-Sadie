<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PlayerRole extends Model
{
    protected $table = 'player_role';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'role_id',
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
