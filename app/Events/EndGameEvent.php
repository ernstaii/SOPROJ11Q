<?php

namespace App\Events;

class EndGameEvent extends GameEvent {

    public function broadcastAs(): string {
        return 'game.end';
    }
}
