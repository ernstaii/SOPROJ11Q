<?php

namespace App\Events;

use App\Enums\Gadgets;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThiefCaughtEvent extends GameEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $game = $user->get_game();
        $this->gameId = $game->id;
        $this->message = 'Boef ' . $user->username . ' is zojuist gevangen!';

        Notification::create([
            'game_id' => $game->id,
            'message' => $this->message
        ]);

        $users = $game->get_users_filtered_on_last_verified();
        event(new GameIntervalEvent($game->id, $users, $game->loot, $this->drone_is_active($users)));
        $game->last_interval_at = Carbon::now();
        $game->save();
    }

    private function drone_is_active($users) {
        foreach ($users as $user)
            if ($user->gadgets()->count() > 0)
                foreach ($user->gadgets() as $gadget)
                    if ($gadget->pivot->in_use && $gadget->name === Gadgets::Drone)
                        return true;

        return false;
    }

    public function broadcastAs()
    {
        return 'thief.caught';
    }
}
