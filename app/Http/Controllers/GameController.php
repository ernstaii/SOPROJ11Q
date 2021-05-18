<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Events\EndGameEvent;
use App\Events\PauseGameEvent;
use App\Events\ResumeGameEvent;
use App\Events\SendNotificationEvent;
use App\Events\StartGameEvent;
use App\Http\Requests\StoreBorderMarkerRequest;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\StoreLootRequest;
use App\Http\Requests\StorePresetRequest;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateGameStateRequest;
use App\Http\Requests\UpdatePoliceStationLocationRequest;
use App\Models\BorderMarker;
use App\Models\Game;
use App\Models\GamePreset;
use App\Models\Loot;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class GameController extends Controller
{
    public function index()
    {
        $gameIds = array();
        foreach (Game::all() as $game)
            array_push($gameIds, $game->id);
        return view('game.index', ['gameIds' => $gameIds]);
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
        if (Request::get('all') === 'true')
            return $game->notifications()->get();
        return $game->notifications()->where('user_id', '=', null)->get();
    }

    public function postNotification(StoreNotificationRequest $request, Game $game)
    {
        return Notification::create([
            'game_id' => $game->id,
            'message' => $request->message,
            'user_id' => $request->user_id
        ]);
    }

    public function getLogo(Game $game)
    {
        $headers = [
            'Content-Type' => 'image/png'
        ];

        return response(base64_decode($game->logo), 200, $headers);
    }

    public function getPresetLoot(GamePreset $preset)
    {
       return $preset->loot()->get();
    }

    public function getPresetBorderMarkers(GamePreset $preset)
    {
        return $preset->border_markers()->get();
    }

    public function checkPassword(Game $game)
    {
        if (Hash::check(Request::get('password'), $game->password))
            return true;
        return false;
    }

    public function show(Game $game)
    {
        if (!Hash::check(Request::get('password'), $game->password))
            return redirect()->route('games.index');

        switch ($game->status) {
            case Statuses::Config:
                return view('config.main', [
                    'police_keys' => $game->get_keys_for_role(Roles::Police),
                    'thief_keys' => $game->get_keys_for_role(Roles::Thief),
                    'border_markers' => $game->border_markers,
                    'id' => $game->id,
                    'loot' => $game->loot,
                    'password' => $game->password,
                    'police_station_location' => $game->police_station_location,
                    'presets' => GamePreset::all()
                ]);
            default:
                $status_text = '';
                if ($game->status == Statuses::Ongoing) {
                    $game->time_left -= Carbon::now()->diffInSeconds(Carbon::parse($game->updated_at));
                    $game->save();
                }
                switch ($game->status) {
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

    public function store(StoreGameRequest $request)
    {
        $game = Game::create([
            'password' => Hash::make($request->password)
        ]);
        return redirect()->route('games.show', [$game, 'password' => $request->password]);
    }

    public function update(UpdateGameStateRequest $request, Game $game)
    {
        if ($game->status === Statuses::Config) {
            $game->duration = $request->duration;
            $game->interval = $request->interval;
            if (isset($request->logo_upload)) {
                if (str_contains($request->logo_upload, 'data:image/png;base64,'))
                    $game->logo = base64_encode(file_get_contents($request->logo_upload));
                else
                    $game->logo = $request->logo_upload;
            }
            if (isset($request->colour))
                $game->colour_theme = $request->colour;
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

        return redirect()->route('games.show', [$game, 'password' => Request::get('password')]);
    }

    public function destroy(Game $game)
    {
        if (!Hash::check(Request::get('password'), $game->password))
            return redirect()->route('games.index');

        $invite_keys = $game->invite_keys();
        $border_markers = $game->border_markers();
        $notifications = $game->notifications();
        $loot = $game->loot();

        $users = new Collection();
        foreach ($invite_keys->get() as $key) {
            $users->push($key->user());
        }

        $invite_keys->delete();
        $border_markers->delete();
        $notifications->delete();
        $loot->delete();

        foreach ($users as $user) {
            $user->delete();
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

	public function sendNotification(StoreNotificationRequest $request, Game $game)
    {
        event(new SendNotificationEvent($game->id, $request->message));
        return redirect()->route('games.show', [$game, 'password' => Request::get('password')]);
    }

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param Game $game
     */
    public function clearExistingMarkers(Game $game)
    {
        $game->border_markers()->delete();
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
                'borderable_id' => $game->id,
                'borderable_type' => Game::class,
                'location' => strval($lats[$i]) . ',' . strval($lngs[$i])
            ]);
        }
    }

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param Game $game
     */
    public function clearExistingLoot(Game $game)
    {
        $game->loot()->delete();
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

        $result = array();
        for ($i = 0; $i < count($lats); $i++) {
            array_push($result, Loot::create([
                'lootable_id' => $game->id,
                'lootable_type' => Game::class,
                'name' => $names[$i],
                'location' => strval($lats[$i]) . ',' . strval($lngs[$i])
            ]));
        }

        return $result;
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


    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param StorePresetRequest $request
     */
    public function storeGamePreset(StorePresetRequest $request)
    {
        $loot_lats = $request->loot_lats;
        $loot_lngs = $request->loot_lngs;
        $loot_names = $request->loot_names;
        $border_lats = $request->border_lats;
        $border_lngs = $request->border_lngs;

        $logo_value = null;
        if (isset($request->logo_upload))
            $logo_value = base64_encode(file_get_contents($request->logo));

        $preset = GamePreset::create([
            'name' => $request->name,
            'duration' => $request->duration,
            'interval' => $request->interval,
            'police_station_location' => $request->police_station_lat . ',' . $request->police_station_lng,
            'colour_theme' => $request->colour_theme,
            'logo' => $logo_value
        ]);

        for ($i = 0; $i < count($loot_lats); $i++) {
            Loot::create([
                'lootable_id' => $preset->id,
                'lootable_type' => GamePreset::class,
                'name' => $loot_names[$i],
                'location' => strval($loot_lats[$i]) . ',' . strval($loot_lngs[$i])
            ]);
        }
        for ($i = 0; $i < count($border_lats); $i++) {
            BorderMarker::create([
                'borderable_id' => $preset->id,
                'borderable_type' => GamePreset::class,
                'location' => strval($border_lats[$i]) . ',' . strval($border_lngs[$i])
            ]);
        }
    }
}
