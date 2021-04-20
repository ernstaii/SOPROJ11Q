<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InviteKey extends Model
{
    use HasFactory;

    protected $primaryKey = 'value';
    public $incrementing = false;

    protected $fillable = [
        'value',
        'game_id',
        'user_id',
        'role',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
