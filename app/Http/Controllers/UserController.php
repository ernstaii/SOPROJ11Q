<?php

namespace App\Http\Controllers;

use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Events\PlayerJoinedGameEvent;
use App\Events\ThiefCaughtEvent;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UpdateSpecialRoleRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\InviteKey;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    public function get(User $user)
    {
        $inviteKey = $user->inviteKey;

        if(isset($inviteKey)) {
            $user->role = $inviteKey->role;
        }

        return $user;
    }

    public function catchThief(User $user)
    {
        if ($user->status != UserStatuses::Playing) {
            response()->json(['errors' => [
                'value' => ['Alleen spelers die niet gevangen of in de lobby zijn kunnen gevangen worden.']
            ]], 422)->throwResponse();
        }

        $user->status = UserStatuses::Caught;
        $user->caught_at = Carbon::now();
        $user->save();
        event(new ThiefCaughtEvent($user));
    }

    public function store(UserStoreRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'location' => $request->location,
        ]);

        $inviteKey = InviteKey::where('value', '=', $request->invite_key)->first();
        $inviteKey->user_id = $user->id;
        $inviteKey->save();

        if (in_array($inviteKey->game->status, [Statuses::Ongoing, Statuses::Paused])) {
            $user->status = UserStatuses::Playing;
            event(new PlayerJoinedGameEvent($inviteKey->game->id, $user));
        }

        $user->save();
        return $user;
    }

    public function update(UpdateLocationRequest $request, User $user)
    {
        $user->location = $request->location;
        $user->last_verified_at = Carbon::now();
        if($user->status != UserStatuses::Caught && $user->status != UserStatuses::Retired) {
            $user->status = UserStatuses::Playing;
        }
        $user->save();

        return $user;
    }

    public function setSpecialRole(UpdateSpecialRoleRequest $request, User $user)
    {
        $user->is_fake_agent = ($request->is_special_role == 'true') ? true : false;
        $user->save();
    }
}
