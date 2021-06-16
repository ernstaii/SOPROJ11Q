<?php

namespace App\Events;

use App\Models\Notification;

class StartGameEvent extends GameEvent
{
    public function __construct($gameId)
    {
        parent::__construct($gameId);
        $this->message = 'Het spel is gestart.';

        Notification::create([
            'game_id' => $gameId,
            'message' => $this->message
        ]);
    }

    public function broadcastAs()
    {
        return 'game.start';
    }
}
