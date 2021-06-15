<?php

namespace App\Http\Controllers;

use App\Enums\Gadgets;
use App\Enums\Roles;
use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Events\EndGameEvent;
use App\Events\GadgetAmountUpdatedEvent;
use App\Events\PauseGameEvent;
use App\Events\PlayerJoinedGameEvent;
use App\Events\ResumeGameEvent;
use App\Events\ScoreUpdatedEvent;
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
use App\Models\Gadget;
use App\Models\Game;
use App\Models\GamePreset;
use App\Models\Loot;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class GameController extends Controller
{
    public function index()
    {
        $game_data = array();
        foreach (Game::all() as $game)
            array_push($game_data, [
                'id' => $game->id,
                'name' => $game->name
            ]);
        return view('game.index', ['game_data' => $game_data]);
    }

    public function get(Game $game)
    {
        return $game;
    }

    public function getUsers(Game $game)
    {
        return $game->get_users();
    }

    public function getUsersWithRoleUnfiltered(Game $game)
    {
        return $game->get_users_with_role_and_gadget_counts();
    }

    public function getUsersWithRole(Game $game)
    {
        $filtered_users = $game->get_users_filtered_on_last_verified();
        $diff = $game->get_users()->diffKeys($filtered_users);

        foreach ($diff as $missing_user){
            if ($missing_user->status != UserStatuses::Disconnected && $missing_user->status != UserStatuses::InLobby){
                Notification::create([
                    'game_id' => $game->id,
                    'message' => $missing_user->username." heeft het spel verlaten"
                ]);
                $missing_user->status = UserStatuses::Disconnected;
                $missing_user->save();
            }
        }

        return $filtered_users;
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
        if (Hash::check(Request::get('password'), $game->password)) {
            if (!Session::has('password') || !(Session::get('password')[0] === $game->password)) {
                Session::put('password', $game->password);
                Session::save();
            }
            return true;
        }
        else {
            Session::remove('password');
        }
        return false;
    }

    public function show($game_name)
    {
        $game = Game::whereName($game_name)->first();

        if ($game == null)
            abort(404);

        if (!(Session::get('password') === $game->password))
            return redirect()->route('games.index');

        switch ($game->status) {
            case Statuses::Config:
                return view('config.main', [
                    'police_keys' => $game->get_keys_for_role(Roles::Police),
                    'thief_keys' => $game->get_keys_for_role(Roles::Thief),
                    'border_markers' => $game->border_markers,
                    'id' => $game->id,
                    'name' => $game->name,
                    'loot' => $game->loot,
                    'police_station_location' => $game->police_station_location,
                    'presets' => GamePreset::all()
                ]);
            default:
                $userIds = array();
                foreach ($game->get_users() as $user)
                    array_push($userIds, $user->id);
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
                    'name' => $game->name,
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
                    'status_text' => $status_text,
                    'userIds' => $userIds
                ]);
        }
    }

    public function store(StoreGameRequest $request)
    {
        $game = Game::create([
            'name' => $request->name,
            'password' => Hash::make($request->password)
        ]);
        if (!Session::has('password') || !(Session::get('password')[0] === $game->password)) {
            Session::put('password', $game->password);
            Session::save();
        }
        return redirect()->route('games.show', ['game_name' => $game->name]);
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
                event(new PauseGameEvent($game->id, $message, $game->time_left));
                break;
        }
        $game->save();

        return redirect()->route('games.show', ['game_name' => $game->name]);
    }

    public function updateThievesScore(Game $game, int $score)
    {
        $game->thieves_score += $score;
        $game->save();

        event(new ScoreUpdatedEvent($game->id, $game->police_score, $game->thieves_score ));

        return $game->thieves_score;
    }

    public function updatePoliceScore(Game $game, int $score)
    {
        $game->police_score += $score;
        $game->save();

        event(new ScoreUpdatedEvent($game->id, $game->police_score, $game->thieves_score ));

        return $game->police_score;
    }

	public function sendNotification(StoreNotificationRequest $request, Game $game)
    {
        event(new SendNotificationEvent($game->id, $request->message));
        return redirect()->route('games.show', ['game_name' => $game->name]);
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
        if (isset($request->logo))
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

    /**
     * AJAX function. Not to be called via manual routing.
     *
     * @param Game $game
     */
    public function addGadgets(Game $game)
    {
        foreach ($game->get_users_with_role() as $user) {
            if ($user->role === Roles::Police) {
                $this->checkGadgets(Gadgets::Alarm, $user);
                $this->checkGadgets(Gadgets::Drone, $user);
            }
            else {
                $this->checkGadgets(Gadgets::Smokescreen, $user);
            }
        }
    }

    private function checkGadgets($gadget_name, User $user)
    {
        $gadgets = $user->gadgets()->get();
        foreach ($gadgets as $gadget) {
            if ($gadget->name === $gadget_name) {
                $gadget->pivot->amount += 1;
                $gadget->pivot->update();
                event(new GadgetAmountUpdatedEvent($user->get_game()->id, $user));
                return;
            }
        }

        $user->gadgets()->attach(Gadget::whereName($gadget_name)->first()->id, array('amount' => 1));
        event(new GadgetAmountUpdatedEvent($user->get_game()->id, $user));
    }
}
