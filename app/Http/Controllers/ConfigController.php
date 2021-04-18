<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Enums\Statuses;
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

    public function createGame(Request $request)
    {

        $game = new Game();
        $game->save();
        $gameId = Game::all()->last()->id;

        return redirect()->route('GameScreen', ['id' => $gameId]);
    }

    public function gameScreen($id)
    {
        $agent_keys = InviteKey::all()->where('game_id', '=', strval($id))->where('role', '=', Roles::Police);
        $thief_keys = InviteKey::all()->where('game_id', '=', strval($id))->where('role', '=', Roles::Thief);
        $game = Game::find($id);

        if ($game != null) {
            switch ($game->status) {
                case Statuses::Config:
                    return view('config.main', compact(['agent_keys', 'thief_keys', 'id']));
                default:
                    return view('game.main', compact(['agent_keys', 'thief_keys', 'id']));
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

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param Request $request
     * @return array
     */
    public function generateKeys(Request $request)
    {
        if (count(Game::find($request->id)->invite_keys) == 0) {
            $total = $request->input;
            $ratio = ($request->ratio / 100);
            $keys = null;
            while ($keys == null) {
                $keys = $this->createKeyStrings($total);
            }
            $totalAgents = round(($total * $ratio), 0, PHP_ROUND_HALF_UP);
            for ($i = 0; $i < $total; $i++) {
                if ($i < $totalAgents) {
                    InviteKey::create([
                        'value' => $keys[$i],
                        'game_id' => $request->id,
                        'role' => Roles::Police,
                    ])->save();
                } else {
                    InviteKey::create([
                        'value' => $keys[$i],
                        'game_id' => $request->id,
                        'role' => Roles::Thief,
                    ])->save();
                }
            }
            return $keys;
        }
        return null;
    }

    private function createKeyStrings($amount) {
        $ALPHANUMERIC_CAPITALS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $keys = [];
        for ($i = 0; $i < $amount; $i++) {
            $key = "";
            for ($j = 0; $j < 4; $j++) {
                $key .= $ALPHANUMERIC_CAPITALS[rand(0, (count($ALPHANUMERIC_CAPITALS) - 1))];
            }
            array_push($keys, $key);
        }
        if(count(array_unique($keys)) < count($keys))
        {
            return null;
        }
        return $keys;
    }
}
