<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notification
 *
 * @property-read \App\Models\Game $game
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @mixin \Eloquent
 */
class Notification extends Model
{
    protected $fillable = [
        'game_id',
        'message',
        'created_at',
        'updated_at'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
