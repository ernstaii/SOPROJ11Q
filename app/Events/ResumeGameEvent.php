<?php

namespace App\Events;

class ResumeGameEvent extends GameEvent
{
    public function broadcastAs()
    {
        return 'game.resume';
    }
}
