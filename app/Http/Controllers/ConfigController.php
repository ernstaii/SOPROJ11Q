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

    public function createGame()
    {
        $game = new Game();
        $game->save();
        $gameId = Game::all()->last()->id;
        return redirect()->route('GameScreen', ['id' => $gameId]);
    }

    public function gameScreen($id)
    {
        $keys = InviteKey::all()->where('game_id', '=', strval($id)); //TODO: get new game id, see if game exists etc.
        if (Game::find($id) != null) {
            return view('config.main', compact(['keys', 'id']));
        }
        return redirect()->route('index');
    }

    public function removeGame($id)
    {
        $keys = InviteKey::all()->where('game_id', '=', $id);
        foreach ($keys as $key) {
            $key->delete();
        }
        Game::destroy($id);
        return redirect()->route('index');
    }

    /**
     * AJAX function. Not to be called via manual routing.
     * @param Request $request
     */
    public function storeKeys(Request $request) {
        foreach ($request->keys as $key) {
            $inviteKey = new InviteKey();
            $inviteKey->value = $key;
            $inviteKey->game_id = $request->id;
            $inviteKey->save();
        }
    }
}
