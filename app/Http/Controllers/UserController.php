<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UserStoreRequest;
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
        $user = User::create([
            'username' => $request->username,
            'location' => $request->location,
        ]);

        $inviteKey = InviteKey::where('value', '=', $request->invite_key);
        $inviteKey->user_id = $user->id;
        $inviteKey->save();

        return $user;
    }

    public function update(UpdateLocationRequest $request, User $user)
    {
        $user->location = $request->location;
        $user->save();

        return $user;
    }
}
