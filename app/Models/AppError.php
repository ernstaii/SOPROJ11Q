<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppError extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'error_id',
        'message',
        'stacktrace',
        'created_at'
    ];
}
