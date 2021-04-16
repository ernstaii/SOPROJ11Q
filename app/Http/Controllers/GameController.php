<?php

namespace App\Http\Controllers;

use App\Models\Game;

class GameController extends Controller
{
    public function getUsersInGame($gameId)
    {
        return Game::find($gameId)->users();
    }

    public function getLootInGame($gameId)
    {
        return Game::find($gameId)->loots()->get();
    }
}
