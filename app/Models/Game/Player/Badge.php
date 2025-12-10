<?php

namespace App\Models\Game\Player;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    protected $table = 'badges';

    public $timestamps = false;

    protected $fillable = ['code'];

    public function playerBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class, 'badge_id');
    }
}
