<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BorderMarker
 *
 * @property int $id
 * @property int $borderable_id
 * @property string $borderable_type
 * @property string $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $borderable
 * @method static \Database\Factories\BorderMarkerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker query()
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker whereBorderableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker whereBorderableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BorderMarker whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BorderMarker extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'borderable_id',
        'borderable_type',
        'location',
        'game_id',
        'created_at',
        'updated_at'
    ];

    public function borderable()
    {
        return $this->morphTo();
    }
}
