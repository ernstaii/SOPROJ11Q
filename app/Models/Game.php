<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Game
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InviteKey[] $invite_keys
 * @property-read int|null $invite_keys_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InviteKey[] $keys
 * @property-read int|null $keys_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Loot[] $loots
 * @property-read int|null $loots_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InviteKey[] $police_invite_keys
 * @property-read int|null $police_invite_keys_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InviteKey[] $thieves_invite_keys
 * @property-read int|null $thieves_invite_keys_count
 * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Game extends Model
{
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

    public function users()
    {
        $keys = $this->invite_keys()->get();

        $users = new Collection();
        foreach ($keys as $key) {
            $users = $users->merge($key->user()->get());
        }

        return $users;
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
