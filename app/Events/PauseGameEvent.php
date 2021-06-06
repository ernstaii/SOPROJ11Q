<?php

namespace App\Events;

use App\Models\Notification;

class PauseGameEvent extends GameEvent
{
    public function __construct($gameId, $message, $time_left)
    {
        $this->gameId = $gameId;
        $this->message = $message;
        $this->timeLeft = $time_left;

        Notification::create([
            'game_id' => $gameId,
            'message' => $message
        ]);
    }

    public function broadcastAs()
    {
        return 'game.pause';
    }
}
