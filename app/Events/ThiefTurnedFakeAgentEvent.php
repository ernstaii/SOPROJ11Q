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

class ThiefTurnedFakeAgentEvent extends GameEvent
{
    public $user;

    public function __construct($gameId, User $user, $is_fake_agent)
    {
        parent::__construct($gameId);
        $this->user = $user;
        if ($is_fake_agent)
            $this->message = 'Speler ' . $user->username . ' is nu een nep agent.';
        else
            $this->message = 'Speler ' . $user->username . ' is nu geen nep agent meer.';
    }

    public function broadcastAs()
    {
        return 'thief.fakeAgent';
    }
}
