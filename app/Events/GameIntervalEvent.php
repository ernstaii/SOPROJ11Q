<?php

namespace App\Events;

class GameIntervalEvent extends GameEvent {

    public function broadcastAs(): string {
        return 'game.interval';
    }
}
