<?php

namespace App\Events;

class PauseGameEvent extends GameEvent {

    public function broadcastAs(): string {
        return 'game.pause';
    }
}
