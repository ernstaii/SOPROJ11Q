<?php

namespace App\Http\Controllers;

use App\Enums\Statuses;
use App\Events\EndGameEvent;
use App\Events\PauseGameEvent;
use App\Events\ResumeGameEvent;
use App\Events\StartGameEvent;
use App\Http\Requests\UpdateGameStateRequest;
use App\Models\Game;
use Carbon\Carbon;
use OutOfBoundsException;

class GameController extends Controller
{
    public function getUsersInGame($gameId)
    {
        return Game::find($gameId)->users();
    }

    public function getLootInGame($gameId)
    {
        return Game::find($gameId)->loots;
    }

    public function getStatusInGame($gameId)
    {
        return Game::find($gameId)->status;
    }

    public function getIntervalInGame($gameId)
    {
        return Game::find($gameId)->interval;
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
                $message = $request->reason;
                if(is_null($message)) {
                    $message = "Het spel is beëindigd!";
                }
                event(new EndGameEvent($id, $message));
                break;
            case Statuses::Paused:
                $game->time_left = $game->time_left - Carbon::now()->diffInSeconds(Carbon::parse($game->updated_at));
                $game->status = $request->state;
                $message = $request->reason;
                if(is_null($message)) {
                    $message = "Het spel is gepauzeerd!";
                }
                event(new PauseGameEvent($id, $message));
                break;
        }
        $game->save();

        return redirect()->route('GameScreen', ['id' => $id]);
    }
}
