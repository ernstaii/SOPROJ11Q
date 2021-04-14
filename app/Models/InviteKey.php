<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InviteKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'game_id',
        'created_at',
        'updated_at',
        'role',
    ];

    public $timestamps = true;

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}
