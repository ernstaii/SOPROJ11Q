<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Services\CustomErrorService;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(User $user): User
    {
        return $user;
    }

    public function store(StoreUserRequest $request)
    {
        $inviteKeyId = $request->get('invite_key');

        // Check if user already exists with inviteKey
        if (User::query()->where('invite_key', $inviteKeyId)->count() > 0) {
            return CustomErrorService::failedApiResponse('Geen toestemming', [
                'value' => ['De code is al in gebruik'],
            ], 403);
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
    public function getInviteKey($inviteKeyId)
    {
        $inviteKey = InviteKey::query()->where('value', $inviteKeyId)->first();

        if (isset($inviteKey)) {
            $totalInUse = User::query()->where('invite_key', $inviteKey->value)->count();

            // Check if InviteKey not yet in use
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
