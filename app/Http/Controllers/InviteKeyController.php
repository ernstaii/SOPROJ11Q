<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Http\Requests\GenerateKeysRequest;
use App\Models\Game;
use App\Models\InviteKey;
use Illuminate\Http\Response;

class InviteKeyController extends Controller
{
    public function get(InviteKey $key)
    {
        if ($key->user()->count() > 0) {
            response()->json([
                'error' => 'De code \'' . $key->value . '\' is al in gebruik.'
            ], 422)->throwResponse();
        }

        return $key;
    }

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param GenerateKeysRequest $request
     * @return array
     * @throws Response
     */
    public function generateKeys(GenerateKeysRequest $request, Game $game)
    {
        if (count($game->invite_keys) == 0) {
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
                        'game_id' => $game->id,
                        'role' => Roles::Police,
                    ]);
                } else {
                    InviteKey::create([
                        'value' => $keys[$i],
                        'game_id' => $game->id,
                        'role' => Roles::Thief,
                    ]);
                }
            }
            return $keys;
        }
        response()->json([
            'error' => 'Er bestaan al keys voor deze game.'
        ], 422)->throwResponse();
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
