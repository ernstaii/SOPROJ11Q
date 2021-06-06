<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Game
 *
 * @property int                                        $id
 * @property string                                     $password
 * @property string                                     $status
 * @property int                                        $duration
 * @property int                                        $interval
 * @property int                                        $time_left
 * @property string|null                                $police_station_location
 * @property int                                        $thieves_score
 * @property int                                        $police_score
 * @property string|null                                $last_interval_at
 * @property string|null                                $started_at
 * @property string|null                                $logo
 * @property string                                     $colour_theme
 * @property \Illuminate\Support\Carbon|null            $created_at
 * @property \Illuminate\Support\Carbon|null            $updated_at
 * @property-read Collection|\App\Models\BorderMarker[] $border_markers
 * @property-read int|null $border_markers_count
 * @property-read Collection|\App\Models\InviteKey[] $invite_keys
 * @property-read int|null $invite_keys_count
 * @property-read Collection|\App\Models\Loot[] $loot
 * @property-read int|null $loot_count
 * @property-read Collection|\App\Models\Notification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\GameFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereColourTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereLastIntervalAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game wherePoliceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game wherePoliceStationLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereThievesScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereTimeLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'password',
        'status',
        'duration',
        'interval',
        'time_left',
        'police_station_location',
        'thieves_score',
        'police_score',
        'last_interval_at',
        'started_at',
        'logo',
        'colour_theme',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function has_keys()
    {
        return $this->hasMany(InviteKey::class)->exists();
    }

    public function loot()
    {
        return $this->morphMany(Loot::class, 'lootable');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function invite_keys()
    {
        return $this->hasMany(InviteKey::class);
    }

    public function border_markers()
    {
        return $this->morphMany(BorderMarker::class, 'borderable');
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

    public function get_users_filtered_on_last_verified()
    {
        if ($this->last_interval_at == null) {
            return $this->get_users_with_role()->all();
        }

        // Because of the delay and the interval, sometimes user last_interval_at will be updated to lately
        $dateTime = Carbon::parse($this->last_interval_at)->addSeconds(-30)->toDateTimeString();

        return $this->get_users_with_role()
            ->where('last_verified_at', '>=', $dateTime)
            ->all();
    }

    public function get_users_with_role()
    {
        $keys = $this->invite_keys()->get();

        $users = new Collection();
        foreach ($keys as $key) {
            $user = $key->user;
            if ($user != null) {
                $user->role = $key->role;
                $users = $users->push($user);
            }
        }

        return $users;
    }

    public function get_keys_for_role(string $role)
    {
        return $this->hasMany(InviteKey::class)->where('role', '=', $role)->get();
    }
}
