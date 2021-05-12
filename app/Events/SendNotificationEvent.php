<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotificationEvent extends GameEvent
{
    public function __construct($gameId, $message)
    {
        $this->gameId = $gameId;
        $this->message = $message;

        Notification::create([
            'game_id' => $gameId,
            'message' => $message
        ]);
    }

    public function broadcastAs()
    {
        return 'game.notification';
    }
}
