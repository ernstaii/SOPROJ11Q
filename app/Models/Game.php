<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'status',
        'duration',
        'interval',
        'time_left',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function hasKeys()
    {
        return $this->hasMany(InviteKey::class)->exists();
    }

    public function loot()
    {
        return $this->belongsToMany(Loot::class, 'game_loot');
    }

    public function invite_keys()
    {
        return $this->hasMany(InviteKey::class);
    }

    public function get_users()
    {
        $keys = $this->invite_keys()->get();

        $users = new Collection();
        foreach ($keys as $key) {
            $users = $users->merge($key->user()->get());
        }

        return $users;
    }

    public function get_users_with_role()
    {

        $keys = $this->invite_keys()->get();

        $users = new Collection();
        foreach ($keys as $key) {
            $user = $key->user()->get();
            $user->put('role', $key->role);
            $users = $users->push($user);
        }

        return $users;
    }

    public function get_keys_for_role(string $role)
    {
        return $this->hasMany(InviteKey::class)->where('role', '=', $role)->get();
    }
}
