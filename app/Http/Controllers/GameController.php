<?php

namespace App\Http\Controllers;

use App\Enums\Statuses;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use function Psy\debug;

class GameController extends Controller
{
    public function getUsersInGame($gameId) {
        return User::query()->whereHas('inviteKey', function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        })->get();
    }

    public function getLootInGame($gameId) {
        return Game::all()->where('id', '=', $gameId)->first()->loots;
    }

    public function updateGameState(Request $request, $id)
    {
        $game = Game::find($id);
        $hasKeys = $game->hasKeys();

        $validated = $request->validate([
            'state' => ['required', 'string'],
            'duration' => ['nullable', 'integer', 'between:10,1440']
        ]);

        $game->duration = $validated['duration'];
        switch ($validated['state']) {
            case Statuses::Ongoing:
                if ($game != null && $hasKeys && ($game->status === Statuses::Config || $game->status === Statuses::Paused)) {
                    $game->status = $validated['state'];
                } else {
                    return redirect()->route('GameScreen', ['id' => $id])->with('errors', ['Het spel kan niet op gaande gezet worden omdat de status van het spel niet correct is.']);
                }
                break;
            case Statuses::Finished:
                if ($game != null && ($game->status === Statuses::Ongoing || $game->status === Statuses::Paused)) {
                    $game->status = $validated['state'];
                } else {
                    return redirect()->route('GameScreen', ['id' => $id])->with('errors', ['Het spel moet gaande of gepauzeerd zijn voordat het spel beëindigd kan worden.']);
                }
                break;
            case Statuses::Paused:
                haha pauzeer spel.call();
                if ($game != null && ($game->status === Statuses::Ongoing)) {
                    $game->status = $validated['state'];
                } else {
                    return redirect()->route('GameScreen', ['id' => $id])->with('errors', ['Het spel moet gaande zijn voordat het spel gepauzeerd kan worden.']);
                }
                break;
            default:
                $game->status = Statuses::Config;
                break;
        }
        $game->save();

        return redirect()->route('GameScreen', ['id' => $id]);
    }
}
