<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'id',
        'status',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function keys()
    {
        return $this->hasMany(InviteKey::class);
    }

    public function hasKeys()
    {
        return $this->hasMany(InviteKey::class)->exists();
    }

    public function loots()
    {
        return $this->belongsToMany(Loot::class, 'game_loot');
    }

    public function invite_keys()
    {
        return $this->hasMany(InviteKey::class);
    }

    public function police_invite_keys()
    {
        return $this->hasMany(InviteKey::class)->where('role', '=', Roles::Police);
    }

    public function thieves_invite_keys()
    {
        return $this->hasMany(InviteKey::class)->where('role', '=', Roles::Thief);
    }
}
