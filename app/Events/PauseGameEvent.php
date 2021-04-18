<?php

namespace App\Events;

class PauseGameEvent extends GameEvent
{
    public function broadcastAs()
    {
        return 'game.pause';
    }
}
