<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class GameEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $gameId;
    protected $message;

    public function __construct($gameId, $message)
    {
        $this->gameId = $gameId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->gameId);
    }

    public abstract function broadcastAs();
}
