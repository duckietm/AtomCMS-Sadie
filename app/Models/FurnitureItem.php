<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FurnitureItem extends Model
{
    use HasFactory;
    protected $table = 'furniture_items';
    public $timestamps = false;
}
