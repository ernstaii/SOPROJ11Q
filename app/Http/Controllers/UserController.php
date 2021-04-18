<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Services\CustomErrorService;
use App\Models\InviteKey;
use App\Models\User;

class UserController extends Controller
{
    public function get(User $user)
    {
        return $user;
    }

    public function store(UserStoreRequest $request)
    {
        $inviteKeyValue = $request->invite_key;
        $gameId = $request->game_id;

        if (User::query()->where('invite_key', $inviteKeyValue)->when('game_id', $gameId)->count() > 0) {
            return CustomErrorService::failedApiResponse('Geen toestemming', [
                'value' => ['De code is al in gebruik'],
            ], 403);
        }

        return User::create([
            'username'   => $request->username,
            'location'   => $request->location,
            'invite_key' => $inviteKeyValue,
            'game_id'    => $gameId,
            'role'       => $request->role,
        ]);
    }

    public function update(UpdateLocationRequest $request, User $user)
    {
        $user->location = $request->location;
        $user->save();

        return $user;
    }

    public function getInviteKeys($inviteKeyValue)
    {
        $inviteKeys = InviteKey::where('value', $inviteKeyValue)->whereNotExists(function ($query) {
            $query->select("invite_key")
                ->from('users')
                ->whereRaw('users.invite_key = invite_keys.value && users.game_id = invite_keys.game_id');
        })->get();

        // Check if there are any invite-keys
        if ($inviteKeys->count() > 0) {
            return $inviteKeys;
        }

        return CustomErrorService::failedApiResponse('Niet gevonden', [
            'value' => ['De code is onjuist of al in gebruik'],
        ], 404);
    }
}
