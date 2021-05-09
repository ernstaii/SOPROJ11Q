<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Events\EndGameEvent;
use App\Events\PauseGameEvent;
use App\Events\ResumeGameEvent;
use App\Events\StartGameEvent;
use App\Http\Requests\StoreBorderMarkerRequest;
use App\Http\Requests\StoreLootRequest;
use App\Http\Requests\UpdateGameStateRequest;
use App\Http\Requests\UpdatePoliceStationLocationRequest;
use App\Models\BorderMarker;
use App\Models\Game;
use App\Models\Loot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GameController extends Controller
{
    public function index()
    {
        return view('game.index', ['games' => Game::all()]);
    }

    public function get(Game $game)
    {
        return $game;
    }

    public function getUsers(Game $game)
    {
        return $game->get_users();
    }

    public function getUsersWithRole(Game $game)
    {
        return $game->get_users_with_role();
    }

    public function getLoot(Game $game)
    {
        return $game->loot()->get();
    }

    public function getInviteKeys(Game $game)
    {
        return $game->invite_keys()->get();
	}

    public function getBorderMarkers(Game $game)
    {
        return $game->border_markers()->get();
    }

    public function getNotifications(Game $game)
    {
        return $game->notifications()->get();
    }

    public function show(Game $game)
    {
        switch ($game->status) {
            case Statuses::Config:
                return view('config.main', [
                    'police_keys' => $game->get_keys_for_role(Roles::Police),
                    'thief_keys' => $game->get_keys_for_role(Roles::Thief),
                    'border_markers' => $game->border_markers,
                    'id' => $game->id,
                    'loot' => $game->loot,
                    'police_station_location' => $game->police_station_location
                ]);
            default:
                $status_text = '';
                if ($game->status == Statuses::Ongoing) {
                    $game->time_left -= Carbon::now()->diffInSeconds(Carbon::parse($game->updated_at));
                    $game->save();
                }
                switch($game->status) {
                    case Statuses::Finished:
                        $status_text = 'Beëindigd';
                        break;
                    case Statuses::Ongoing:
                        $status_text = 'Gaande';
                        break;
                    case Statuses::Paused:
                        $status_text = 'Gepauzeerd';
                        break;
                    default:
                        $status_text = 'Configuratie-modus';
                        break;
                }
                return view('game.main', [
                    'id' => $game->id,
                    'loot' => $game->loot,
                    'users' => $game->get_users_with_role(),
                    'border_markers' => $game->border_markers,
                    'interval' => $game->interval,
                    'duration' => $game->duration,
                    'game_status' => $game->status,
                    'time_left' => $game->time_left,
                    'notifications' => $game->notifications,
                    'thieves_score' => $game->thieves_score,
                    'police_score' => $game->police_score,
                    'police_station_location' => $game->police_station_location,
                    'status_text' => $status_text
                ]);
        }
    }

    public function store()
    {
        $game = Game::create();
        return redirect()->route('games.show', [$game]);
    }

    public function update(UpdateGameStateRequest $request, Game $game)
    {
        if ($game->status === Statuses::Config) {
            $game->duration = $request->duration;
            $game->interval = $request->interval;
            $game->jail_time = $request->jail_time;
        }

        switch ($request->state) {
            case Statuses::Ongoing:
                if ($game->status === Statuses::Config) {
                    $game->time_left = $request->duration * 60;
                    $game->started_at = Carbon::now();

                    $users = $game->get_users();
                    foreach ($users as $user) {
                        $user->status = UserStatuses::Playing;
                        $user->save();
                    }

                    event(new StartGameEvent($game->id));
                } else {
                    event(new ResumeGameEvent($game->id, $game->time_left));
                }
                $game->status = $request->state;
                break;
            case Statuses::Finished:
                $game->status = $request->state;
                $game->time_left = 0;
                $message = $request->reason;
                if (is_null($message)) {
                    $message = "Het spel is beëindigd!";
                }

                $users = $game->get_users();
                foreach ($users as $user) {
                    $user->status = UserStatuses::Retired;
                    $user->save();
                }

                event(new EndGameEvent($game->id, $message));
                break;
            case Statuses::Paused:
                $game->time_left = $game->time_left - Carbon::now()->diffInSeconds(Carbon::parse($game->updated_at));
                $game->status = $request->state;
                $message = $request->reason;
                if (is_null($message)) {
                    $message = "Het spel is gepauzeerd!";
                }
                event(new PauseGameEvent($game->id, $message));
                break;
        }
        $game->save();

        return redirect()->route('games.show', [$game]);
    }

    public function destroy(Game $game)
    {
        $invite_keys = $game->invite_keys();
        $border_markers = $game->border_markers();

        $users = new Collection();
        foreach ($invite_keys->get() as $key) {
            $users->push($key->user());
        }

        $invite_keys->delete();
        $border_markers->delete();

        foreach ($users as $user) {
            $user->delete();
        }

        $old_loot = $game->loot()->get();
        $game->loot()->detach();
        foreach ($old_loot as $loot_item) {
            $loot_item->delete();
        }

        $game->delete();

        return redirect()->route('games.index');
    }

    public function updateThievesScore(Game $game, int $score)
	{
        $game->thieves_score = $score;
        $game->save();

        return $game;
    }

    public function updatePoliceScore(Game $game, int $score)
	{
        $game->police_score = $score;
        $game->save();

        return $game;
	}

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param StoreBorderMarkerRequest $request
     * @param Game $game
     */
    public function storeMarkers(StoreBorderMarkerRequest $request, Game $game)
    {
        $lats = $request->lats;
        $lngs = $request->lngs;
        for ($i = 0; $i < count($lats); $i++) {
            BorderMarker::create([
                'location' => strval($lats[$i]) . ',' . strval($lngs[$i]),
                'game_id' => $game->id
            ]);
        }
    }

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param StoreLootRequest $request
     * @param Game $game
     */
    public function storeLoot(StoreLootRequest $request, Game $game)
    {
        $lats = $request->lats;
        $lngs = $request->lngs;
        $names = $request->names;
        for ($i = 0; $i < count($lats); $i++) {
            $newLoot = Loot::create([
                'name' => $names[$i],
                'location' => strval($lats[$i]) . ',' . strval($lngs[$i])
            ]);
            $game->loot()->attach($newLoot);
        }
    }

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param UpdatePoliceStationLocationRequest $request
     * @param Game $game
     */
    public function setPoliceStationLocation(UpdatePoliceStationLocationRequest $request, Game $game)
    {
        $lat = $request->lat;
        $lng = $request->lng;

        $game->police_station_location = strval($lat) . ',' . strval($lng);
        $game->save();
    }
}
