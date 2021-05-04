<?php

namespace App\Events;

use App\Enums\UserStatuses;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThiefCaughtEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $game = $user->get_game();
        $this->gameId = $game->id;
        $this->user = $user;

        event(new GameIntervalEvent($game->id, $game->get_users()->where('status', '=', UserStatuses::Playing)));
        $game->last_interval_at = Carbon::now();
        $game->save();
    }

    public function broadcastAs()
    {
        return 'thief.caught';
    }
}
