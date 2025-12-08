<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'hidden_staff' => 'boolean',
        'hidden_rank'  => 'boolean',
    ];

    public function playerRoles(): HasMany
    {
        return $this->hasMany(PlayerRole::class, 'role_id');
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            PlayerRole::class,
            'role_id',
            'id',
            'id',
            'player_id',
        );
    }
}
