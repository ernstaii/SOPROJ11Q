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
        $inviteKeyId = $request->invite_key;

        return User::create([
            'username' => $request->username,
            'location' => $request->location,
            'invite_key' => $inviteKeyId,
            'role' => $request->role
        ]);
    }

    public function update(UpdateLocationRequest $request, User $user)
    {
        $user->location = $request->location;
        $user->save();
        return $user;
    }

    public function getInviteKey($inviteKeyId)
    {
        $inviteKey = InviteKey::where('value', $inviteKeyId)->first();
        if (isset($inviteKey)) {
            $totalInUse = User::query()->where('invite_key', $inviteKey->value)->count();

            if ($totalInUse == 0) {
                return $inviteKey;
            }

            return CustomErrorService::failedApiResponse('Geen toestemming', [
                'value' => ['De code is al in gebruik'],
            ], 403);
        }

        return CustomErrorService::failedApiResponse('Niet gevonden', [
            'value' => ['De code is onjuist'],
        ], 404);
    }
}
