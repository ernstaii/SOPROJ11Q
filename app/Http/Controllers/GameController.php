<?php

namespace App\Http\Controllers;

use App\Enums\Statuses;
use App\Events\EndGameEvent;
use App\Events\PauseGameEvent;
use App\Events\ResumeGameEvent;
use App\Events\StartGameEvent;
use App\Http\Requests\UpdateGameStateRequest;
use App\Models\Game;
use App\Models\User;
use Carbon\Carbon;

class GameController extends Controller
{
    public function getUsersInGame($gameId)
    {
        return User::query()->whereHas('inviteKey', function ($query) use ($gameId) {
            return $query->where('game_id', $gameId);
        })->get();
    }

    public function getLootInGame($gameId)
    {
        return Game::all()->where('id', '=', $gameId)->first()->loots;
    }

    public function getStatusInGame($gameId)
    {
        return Game::all()->where('id', '=', $gameId)->first()->status;
    }

    public function updateGameState(UpdateGameStateRequest $request, $id)
    {
        $game = Game::find($id);

        if ($game->status === Statuses::Config) {
            $game->duration = $request->duration;
            $game->interval = $request->interval;
        }

        switch ($request->state) {
            case Statuses::Ongoing:
                if ($game->status === Statuses::Config) {
                    $game->time_left = $request->duration * 60;
                    event(new StartGameEvent($id));
                } else {
                    event(new ResumeGameEvent($id));
                }
                $game->status = $request->state;
                break;
            case Statuses::Finished:
                $game->status = $request->state;
                $game->time_left = 0;
                event(new EndGameEvent($id));
                break;
            case Statuses::Paused:
                $game->time_left = $game->time_left - Carbon::now()->diffInSeconds(Carbon::parse($game->updated_at));
                $game->status = $request->state;
                event(new PauseGameEvent($id));
                break;
            default:
                $game->status = Statuses::Config;
                break;
        }
        $game->save();

        return redirect()->route('GameScreen', ['id' => $id]);
    }
}
