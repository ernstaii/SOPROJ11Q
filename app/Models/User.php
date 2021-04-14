<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'id',
        'username',
        'location',
        'invite_key',
        'role',
    ];

    public function inviteKey()
    {
        return $this->belongsTo(InviteKey::class, "invite_key", "value");
    }
}
