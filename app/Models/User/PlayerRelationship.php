<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerRelationship extends Model
{
    protected $table = 'player_relationships';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function origin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'origin_player_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_player_id');
    }

    public function type()
    {
        return $this->belongsTo(PlayerRelationshipType::class, 'type_id');
    }
}
