<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GadgetAmountUpdatedEvent extends GameEvent
{
    public $gadgets;
    public $user;

    public function __construct($gameId, User $user)
    {
        parent::__construct($gameId);
        $this->user = $user;
        $this->gadgets = $user->gadgets()->get();
    }

    public function broadcastAs()
    {
        return 'gadgets.update';
    }
}
