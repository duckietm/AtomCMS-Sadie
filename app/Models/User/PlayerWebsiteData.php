<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PlayerWebsiteData extends Model
{
    protected $table = 'player_website_data';

    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'initial_ip',
        'last_ip',
        'last_login',
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
