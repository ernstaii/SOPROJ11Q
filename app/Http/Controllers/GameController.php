<?php

namespace App\Http\Controllers;

use App\Models\User;

class GameController extends Controller
{
    public function getUsersInGame($gameId)
    {
        return dd(User::query()->whereHas('inviteKey', function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        })->get());
    }
}
