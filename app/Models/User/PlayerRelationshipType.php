<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class PlayerRelationshipType extends Model
{
    protected $table = 'player_relationship_types';

    public $timestamps = false;

    protected $guarded = ['id'];
}
