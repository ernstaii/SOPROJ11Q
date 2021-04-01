<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InviteKey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'game_id',
        'created_at',
        'updated_at',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}
