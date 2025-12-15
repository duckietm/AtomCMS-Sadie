<?php

namespace App\Models\Game;

use App\Models\Compositions\HasBadge;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model implements HasBadge
{
    protected $table = 'roles';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'player_role', 'role_id', 'player_id');
    }

    public function getBadgePath(): string
    {
        return sprintf('%s%s.gif', setting('badges_path'), $this->getBadgeName());
    }

    public function getBadgeName(): string
    {
        return (string) ($this->badge ?? '');
    }
		
	public function permissions(): BelongsToMany
	{
		return $this->belongsToMany(Permission::class, 'roles_permissions', 'role_id', 'permission_id');
	}
}
