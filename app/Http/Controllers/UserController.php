<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Services\CustomErrorService;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function get(User $user)
    {
        return $user;
    }

    public function store(StoreUserRequest $request)
    {
        $inviteKeyValue = $request->get('invite_key');
        $gameId = $request->get('game_id');

        // Check if user already exists with inviteKey
        if (User::query()->where('invite_key', $inviteKeyValue)->where('game_id', $gameId)->count() > 0) {
            return CustomErrorService::failedApiResponse('Geen toestemming', [
                'value' => ['De code is al in gebruik'],
            ], 403);
        }

        $user = User::create([
            'username'   => $request->get('username'),
            'location'   => $request->get('location'),
            'invite_key' => $inviteKeyValue,
            'game_id'    => $gameId,
            'role'       => $request->get('role'),
        ]);

        $user->save();

        return $user;
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i',
        ]);

        $user->location = $request->get('location');
        $user->save();
    }

    public function getInviteKeys($inviteKeyId)
    {
        $inviteKeys = InviteKey::query()->where('value', $inviteKeyId)->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
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
