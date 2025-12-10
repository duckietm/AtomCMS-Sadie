<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerSsoToken extends Model
{
    protected $table = 'player_sso_tokens';

    public $timestamps = false; // table has no updated_at

    protected $fillable = [
        'player_id',
        'token',
        'created_at',
        'expires_at',
        'used_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
