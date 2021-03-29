<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\InviteKey;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $games = Game::all();
        return view('main_screen', compact(['games']));
    }

    public function gameScreen()
    {
        $game = new Game();
        $game->name = "test";
        $game->save();
        $keys = InviteKey::all()->where('game_id', '=', strval($game->id)); //TODO: get new game id, see if game exists etc.
        return view('config.main', compact(['keys']));
    }

    /**
     * AJAX function. Not to be called via manual routing.
     * @param Request $request
     */
    public function storeKeys(Request $request) {
        foreach ($request->keys as $key) {
            $inviteKey = new InviteKey();
            $inviteKey->value = $key;
            $inviteKey->game_id = 1;
            $inviteKey->save();
        }
    }
}
