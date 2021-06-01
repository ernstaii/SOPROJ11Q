<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GamePreset
 *
 * @property int $id
 * @property string $name
 * @property string $duration
 * @property string $interval
 * @property string|null $police_station_location
 * @property string $colour_theme
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BorderMarker[] $border_markers
 * @property-read int|null $border_markers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Loot[] $loot
 * @property-read int|null $loot_count
 * @method static \Database\Factories\GamePresetFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset query()
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereColourTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset wherePoliceStationLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePreset whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GamePreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'interval',
        'police_station_location',
        'colour_theme',
        'logo',
        'created_at',
        'updated_at'
    ];

    public function loot()
    {
        return $this->morphMany(Loot::class, 'lootable');
    }

    public function border_markers()
    {
        return $this->morphMany(BorderMarker::class, 'borderable');
    }
}
