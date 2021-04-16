<?php

namespace Tests\Feature;

use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_user()
    {
        $user = User::factory()->make();

        $response = $this->call('POST', '/api/users', [
            'username' => $user->getAttribute('username'),
            'location' => $user->getAttribute('location'),
            'role' => $user->getAttribute('role'),
            'invite_key' => $user->getAttribute('invite_key'),
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'username' => $user->getAttribute('username'),
            'location' => $user->getAttribute('location'),
            'role' => $user->getAttribute('role'),
            'invite_key' => $user->getAttribute('invite_key'),
        ]);
    }

    public function test_can_update_location()
    {
        //'51.498134,-0.201755'
        $user = User::factory()->create();
        $userId = $user->getKey();

        $response = $this->call('PUT', "/api/users/$userId", [
            'location' => '51.498134,-0.201754',
        ]);

        $response->assertStatus(200);

        $this->assertEquals("51.498134,-0.201754", User::find($userId)->location);
    }

    public function test_can_get_user()
    {
        $user = User::factory()->create();
        $userId = $user->getKey();

        $response = $this->call('GET', "/api/users/$userId");

        $response->assertStatus(200)->assertExactJson([
            'id' => $userId,
            'username' => $user->username,
            'location' => $user->location,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'invite_key' => $user->invite_key,
            'role' => $user->role,
        ]);
    }

    public function test_can_get_users()
    {
        $user = User::factory()->create();
        $inviteKey = InviteKey::where('value', $user->invite_key)->first();

        $response = $this->call('GET', "/api/game/$inviteKey->game_id/users");

        $response->assertStatus(200)->assertExactJson([
            [
                'id' => $user->getKey(),
                'username' => $user->username,
                'location' => $user->location,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'invite_key' => $user->invite_key,
                'role' => $user->role,
            ]
        ]);
    }
}
