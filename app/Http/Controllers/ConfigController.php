<?php

namespace App\Http\Controllers;

use App\Enums\Statuses;
use App\Models\Game;
use App\Models\InviteKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        $keys = InviteKey::all()->where('game_id', '=', strval($id));
        $game = Game::find($id);
        if ($game != null) {
            switch ($game->status) {
                case Statuses::Ongoing:
                    return view('game.main', compact(['keys', 'id']));
                default:
                    return view('config.main', compact(['keys', 'id']));
            }
        }

        return redirect()->route('index');
    }

    public function removeGame($id)
    {
        InviteKey::where('game_id', $id)->delete();
        Game::destroy($id);

        return redirect()->route('index');
    }

    public function startGame($id)
    {
        $game = Game::find($id);
        $hasKeys = $game->hasKeys();

        if ($game != null && $hasKeys) {
            $game->status = Statuses::Ongoing;
            $game->save();
        } else {
            return redirect()->route('GameScreen', ['id' => $id])->with('errors', ['Er moeten invite codes bestaan voordat het spel gestart kan worden.']);
        }

        return redirect()->route('GameScreen', ['id' => $id]);
    }

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param  Request $request
     */
    public function storeKeys(Request $request)
    {
        foreach ($request->keys as $key) {
            InviteKey::create([
                'value'   => $key,
                'game_id' => $request->id,
            ])->save();
        }
    }
}
