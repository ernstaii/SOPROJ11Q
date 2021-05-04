<?php

namespace App\Events;

use App\Models\Notification;

class ResumeGameEvent extends GameEvent
{
    public function broadcastAs()
    {
        return 'game.resume';
    }
}
