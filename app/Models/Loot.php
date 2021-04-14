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
        'game_id',
        'name',
        'location'
    ];

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_loot');
    }
}
