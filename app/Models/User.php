<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'username',
        'location',
        'invite_key',
        'role',
    ];

    public function inviteKey(): BelongsTo
    {
        return $this->belongsTo(InviteKey::class, "invite_key", "value");
    }
}
