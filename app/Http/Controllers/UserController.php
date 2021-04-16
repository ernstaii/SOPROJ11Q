<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(User $user)
    {
        return $user;
    }

    public function store(UserStoreRequest $request)
    {
        $inviteKeyId = $request->invite_key;

        if (User::where('invite_key', $inviteKeyId)->exists()) {
            return null;
        }
        $role = $request->role;
        return User::create([
            'username' => $request->username,
            'location' => $request->location,
            'invite_key' => $inviteKeyId,
            'role' => isset($role) ? $role : Roles::Thief,
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
            // TODO: Player could have an InviteCode with the same value, but then InviteCode can't have same TeamId
            if (User::where('invite_key', $inviteKey->value)->count() == 0) {
                return $inviteKey;
            }
            return response()->json(['error' => 'De code is al in gebruik'], 403);
        }
        return response()->json(['error' => 'De code is onjuist'], 404);
    }
}
