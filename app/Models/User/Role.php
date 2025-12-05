<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'hidden_staff' => 'boolean',
        'hidden_rank' => 'boolean',
    ];

    public function playerRoles(): HasMany
    {
        return $this->hasMany(PlayerRole::class, 'role_id');
    }
}
