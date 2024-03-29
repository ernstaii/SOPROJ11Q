<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property int                             $id
 * @property string                          $username
 * @property string|null                     $location
 * @property string                          $status
 * @property int|null                        $triggered_alarm
 * @property string|null                     $caught_at
 * @property boolean|null                    $is_fake_agent
 * @property string                          $last_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Gadget[] $gadgets
 * @property-read int|null $gadgets_count
 * @property-read \App\Models\InviteKey|null $inviteKey
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCaughtAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsFakeAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTriggeredAlarm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'id',
        'username',
        'location',
        'status',
        'triggered_alarm',
        'caught_at',
        'last_verified_at',
        'is_fake_agent',
    ];

    public function inviteKey()
    {
        return $this->hasOne(InviteKey::class, "user_id", "id");
    }

    public function get_game()
    {
        return $this->inviteKey->game;
    }

    public function gadgets()
    {
        return $this->belongsToMany(Gadget::class, 'gadgets_users')->withPivot(['amount', 'in_use', 'location', 'activated_at']);
    }
}
