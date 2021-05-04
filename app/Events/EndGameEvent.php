<?php

namespace App\Events;

use App\Models\Notification;

class EndGameEvent extends GameEvent
{
    public function __construct($gameId, $message)
    {
        $this->gameId = $gameId;
        $this->message = $message;

        Notification::create([
            'game_id' => $gameId,
            'message' => $message
        ]);
    }

    public function broadcastAs()
    {
        return 'game.end';
    }
}
