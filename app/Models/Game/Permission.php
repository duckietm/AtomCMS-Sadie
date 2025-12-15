<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'permissions';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'roles_permissions',
            'permission_id',
            'role_id'
        );
    }
}
