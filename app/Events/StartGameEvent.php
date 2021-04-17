<?php

namespace App\Events;

class StartGameEvent extends GameEvent
{
    public function broadcastAs()
    {
        return 'game.start';
    }
}
