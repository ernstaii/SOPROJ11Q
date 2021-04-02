<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(User $user): User
    {
        return $user;
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'   => 'required|min:3|max:255',
            'location'   => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i',
            'invite_key' => 'required|exists:invite_keys,value',
        ]);

        $inviteKeyId = $request->get('invite_key');

        // Check if user already exists with inviteKey
        if (User::query()->where('invite_key', $inviteKeyId)->count() > 0) {
            return null;
        }

        $user = User::create([
            'username'   => $request->get('username'),
            'location'   => $request->get('location'),
            'invite_key' => $inviteKeyId,
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

    // Return InviteKey based on value
    // Key could be null, if InviteKey doesn't exists anymore
    public function getInviteKey($inviteKeyId)
    {
        $inviteKey = InviteKey::query()->where('value', $inviteKeyId)->first();

        if (! isset($inviteKey)) {
            return null;
        }

        // TODO: Player could have an InviteCode with the same value, but then InviteCode can't have same TeamId
        $totalInUse = User::query()->where('invite_key', $inviteKey->value)->count();

        // Check if InviteKey not yet in use
        return $totalInUse == 0 ? $inviteKey : null;
    }
}
