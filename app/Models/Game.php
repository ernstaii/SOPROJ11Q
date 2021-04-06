<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'status',
        'created_at',
        'updated_at'
    ];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function keys() {
        return $this->hasMany(InviteKey::class);
    }

    public function hasKeys(): bool {
        return $this->hasMany(InviteKey::class)->exists();
    }

    public function loots()
    {
        return $this->belongsToMany(Loot::class, 'game_loot');
    }
}
