<?php

namespace App\Events;

class ResumeGameEvent extends GameEvent {

    public function broadcastAs(): string {
        return 'game.resume';
    }
}
