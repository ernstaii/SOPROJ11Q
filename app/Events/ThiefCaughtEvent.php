<?php

namespace App\Events;

use App\Enums\UserStatuses;
use App\Models\Notification;
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
        $this->user = $user;
        $game = $user->get_game();
        $this->gameId = $game->id;
        $this->message = 'Boef ' . $user->username . ' is zojuist gevangen!';

        event(new GameIntervalEvent($game->id, $game->get_users_with_role()->where('status', '=', UserStatuses::Playing), $game->loot));
        $game->last_interval_at = Carbon::now();
        $game->save();
    }

    public function broadcastAs()
    {
        return 'thief.caught';
    }
}
