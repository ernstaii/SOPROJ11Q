<?php

namespace App\Events;

use App\Enums\UserStatuses;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThiefReleasedEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(User $user)
    {
        $game = $user->get_game();
        $this->gameId = $game->id;
        $this->message = 'Boef ' . $user->username . ' is zojuist vrijgelaten.';

        Notification::create([
            'game_id' => $game->id,
            'message' => $this->message
        ]);

        event(new GameIntervalEvent($game->id, $game->get_users()->where('status', '=', UserStatuses::Playing), $game->loot));
        $game->last_interval_at = Carbon::now();
        $game->save();
    }

    public function broadcastAs()
    {
        return 'thief.released';
    }
}
