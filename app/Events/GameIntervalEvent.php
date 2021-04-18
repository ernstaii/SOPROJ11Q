<?php

namespace App\Events;

class GameIntervalEvent extends GameEvent
{
    public function broadcastAs()
    {
        return 'game.interval';
    }
}
