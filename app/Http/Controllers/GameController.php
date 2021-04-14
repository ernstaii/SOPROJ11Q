<?php

namespace App\Http\Controllers;

use App\Models\Game;

class GameController extends Controller
{
    public function getUsersInGame($gameId)
    {
        return Game::find($gameId)->users()->get();
//        return User::query()->whereHas('inviteKey', function ($query) use ($gameId) {
//            return $query->where('game_id', $gameId);
//        })->get();
    }

    public function getLootInGame($gameId)
    {
        return Game::find($gameId)->loots()->get();
    }
}
