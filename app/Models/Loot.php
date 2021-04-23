<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Loot
 *
 * @property int $id
 * @property string $name
 * @property string $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Game[] $games
 * @property-read int|null $games_count
 * @method static \Database\Factories\LootFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Loot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loot query()
 * @method static \Illuminate\Database\Eloquent\Builder|Loot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loot whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Loot extends Model
{
    use HasFactory;

    protected $table = "loot";

    protected $fillable = [
        'id',
        'name',
        'location',
        'game_id'
    ];

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_loot');
    }
}
