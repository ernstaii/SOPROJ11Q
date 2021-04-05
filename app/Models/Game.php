<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function loot() {
        return $this->hasMany(Loot::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function loots()
    {
        return $this->belongsToMany(Loot::class);
    }
}
