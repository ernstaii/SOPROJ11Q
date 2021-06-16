<?php

namespace App\Events;

use App\Enums\Gadgets;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ThiefCaughtEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    private $smokescreened_users;

    public function __construct(User $user)
    {
        $fullUser = $user->toArray();
        $fullUser["role"] = $user->inviteKey->role;
        $this->user = $fullUser;

        $game = $user->get_game();
        parent::__construct($game->id);

        $this->message = 'Boef ' . $user->username . ' is zojuist gevangen!';

        Notification::create([
            'game_id' => $game->id,
            'message' => $this->message
        ]);
    }

    public function broadcastAs()
    {
        return 'thief.caught';
    }
}
