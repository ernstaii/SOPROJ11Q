<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InviteKey
 *
 * @property string $value
 * @property int $game_id
 * @property int|null $user_id
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\InviteKeyFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InviteKey whereValue($value)
 * @mixin \Eloquent
 */
class InviteKey extends Model
{
    use HasFactory;

    protected $primaryKey = 'value';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'value',
        'game_id',
        'user_id',
        'role',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
