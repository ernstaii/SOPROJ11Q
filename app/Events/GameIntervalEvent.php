<?php

namespace App\Events;

use App\Enums\Gadgets;
use App\Models\Game;
use Carbon\Carbon;

class GameIntervalEvent extends GameEvent
{
    const ALARM_ACTIVE_TIME = 300;

    public $users;
    public $loot;
    public $drone_is_active;

    public function __construct($gameId, $users, $loot, $drone_is_active, $time_left)
    {
        $this->gameId = $gameId;
        $this->timeLeft = $time_left;

        $this->users = [];
        foreach ($users as $user){
            array_push($this->users, $user);
        }

        $this->loot = $loot;
        $this->drone_is_active = $drone_is_active;

        $this->check_alarms(Game::findOrFail($gameId)->first());
    }

    public function broadcastAs()
    {
        return 'game.interval';
    }


    private function check_alarms(Game $game)
    {
        $users = $game->get_users();

        foreach ($users as $user) {
            if ($user->triggered_alarm) {
                $user->triggered_alarm = null;
                $user->save();
            }

            if ($user->gadgets()->count() > 0)
                foreach ($user->gadgets()->get as $gadget)
                    if ($gadget->pivot->in_use && $gadget->name === Gadgets::Alarm)
                        if (Carbon::parse($gadget->pivot->activated_at)->diffInSeconds(Carbon::now()) >= self::ALARM_ACTIVE_TIME) {
                            $gadget->pivot->in_use = null;
                            $gadget->pivot->location = null;
                            $gadget->pivot->activated_at = null;
                            $gadget->pivot->save();
                        }
        }
    }
}
