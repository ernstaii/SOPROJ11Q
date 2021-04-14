<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(User $user)
    {
        return $user;
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|min:3|max:255',
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i',
            'invite_key' => 'required|exists:invite_keys,value',
        ]);

        $inviteKeyId = $request->get('invite_key');

        if (User::where('invite_key', $inviteKeyId)->exists()) {
            return null;
        }
        $role = $request->get('role');
        return User::create([
            'username' => $request->get('username'),
            'location' => $request->get('location'),
            'invite_key' => $inviteKeyId,
            'role' => isset($role) ? $role : Roles::Thief,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'location' => 'nullable|regex:/^([-+]?)([\d]{1,2})(((\.)(\d+)(,)))(\s*)(([-+]?)([\d]{1,3})((\.)(\d+))?)$/i',
        ]);
        $user->location = $request->get('location');
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
