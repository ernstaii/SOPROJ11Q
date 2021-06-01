<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Gadget
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Gadget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gadget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gadget query()
 * @method static \Illuminate\Database\Eloquent\Builder|Gadget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gadget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gadget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gadget whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Gadget extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'gadgets_users')->withPivot(['amount', 'in_use', 'location', 'activated_at']);
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
}
