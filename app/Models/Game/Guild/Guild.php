<?php

namespace App\Models\Game\Guild;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guild extends Model
{
    protected $table = 'groups';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function members(): HasMany
    {
        return $this->hasMany(GuildMember::class, 'group_id', 'id');
    }
}
