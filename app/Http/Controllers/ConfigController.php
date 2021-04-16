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
        return view('main_screen', ['games' => Game::all()]);
    }

    public function storeGame()
    {
        $game = Game::create();
        return redirect()->route('GameScreen', ['id' => $game->id]);
    }

    public function gameScreen($id)
    {
        $police_keys = InviteKey::where('game_id', '=', strval($id))->where('role', '=', Roles::Police)->get();
        $thief_keys = InviteKey::where('game_id', '=', strval($id))->where('role', '=', Roles::Thief)->get();
        $game = Game::find($id);

        if (isset($game)) {
            switch ($game->status) {
                case Statuses::Config:
                    return view('config.main', compact(['police_keys', 'thief_keys', 'id']));
                default:
                    return view('game.main', compact(['police_keys', 'thief_keys', 'id']));
            }
        }
        return redirect()->route('index');
    }

    public function removeGame($id)
    {
        Game::find($id)->invite_keys()->delete();
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
            $keys = null;
            while (!isset($keys)) {
                $keys = $this->createKeyStrings($total);
            }
            $totalAgents = round(($total * ($request->ratio / 100)), 0, PHP_ROUND_HALF_UP);
            for ($i = 0; $i < $total; $i++) {
                if ($i < $totalAgents) {
                    InviteKey::create([
                        'value' => $keys[$i],
                        'game_id' => $request->id,
                        'role' => Roles::Police,
                    ]);
                } else {
                    InviteKey::create([
                        'value' => $keys[$i],
                        'game_id' => $request->id,
                        'role' => Roles::Thief,
                    ]);
                }
            }
            return $keys;
        }
        return null;
    }

    private function createKeyStrings($amount)
    {
        $ALPHANUMERIC_CAPITALS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $keys = array();
        for ($i = 0; $i < $amount; $i++) {
            $key = "";
            for ($j = 0; $j < 4; $j++) {
                $key .= $ALPHANUMERIC_CAPITALS[rand(0, (count($ALPHANUMERIC_CAPITALS) - 1))];
            }
            array_push($keys, $key);
        }
        if (count(array_unique($keys)) < count($keys)) {
            return null;
        }
        return $keys;
    }
}
