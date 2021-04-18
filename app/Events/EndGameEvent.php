<?php

namespace App\Events;

class EndGameEvent extends GameEvent
{
    public function broadcastAs()
    {
        return 'game.end';
    }
}
