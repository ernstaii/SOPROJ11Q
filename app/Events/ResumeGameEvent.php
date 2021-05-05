<?php

namespace App\Events;

use App\Models\Notification;

class ResumeGameEvent extends GameEvent
{
    public function __construct($gameId)
    {
        $this->gameId = $gameId;
        $this->message = 'Het spel is hervat.';

        Notification::create([
            'game_id' => $gameId,
            'message' => $this->message
        ]);
    }

    public function broadcastAs()
    {
        return 'game.resume';
    }
}
