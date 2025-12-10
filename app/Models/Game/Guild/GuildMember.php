<?php

namespace App\Models\Game\Guild;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuildMember extends Model
{
    protected $table = 'group_player';

    public $timestamps = false;

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'group_id',
        'player_id',
    ];

    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
