<?php

namespace App\Events;

class PauseGameEvent extends GameEvent
{
    public $message;

    public function __construct($gameId, $message)
    {
        $this->gameId = $gameId;
        $this->message = $message;
    }

    public function broadcastAs()
    {
        return 'game.pause';
    }
}
