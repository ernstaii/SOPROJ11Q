<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loot extends Model
{
    use HasFactory;

    protected $table = "loot";

    protected $fillable = [
        'id',
        'name',
        'location',
        'game_id'
    ];

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_loot');
    }
}
