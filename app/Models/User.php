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
        'location'
    ];

    public function inviteKey()
    {
        return $this->hasOne(InviteKey::class, "user_id", "id");
    }
}
