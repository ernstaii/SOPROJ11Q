<?php

namespace Tests\Feature;

use App\Models\Game;
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

        $response = $this->post('/api/users', [
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

    public function test_cannot_store_user_with_used_key()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/users', [
            'username' => 'test_user_2',
            'location' => '51.498134,-0.201754',
            'invite_key' => $user->invite_key
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors();
    }

    public function test_can_update_location()
    {
        //'51.498134,-0.201755'
        $user = User::factory()->create();
        $userId = $user->getKey();

        $response = $this->put("/api/users/$userId", [
            'location' => '51.498134,-0.201754',
        ]);

        $response->assertStatus(200);

        $this->assertEquals("51.498134,-0.201754", User::find($userId)->location);
    }

    public function test_can_get_user()
    {
        $user = User::factory()->create();
        $userId = $user->getKey();

        $response = $this->get("/api/users/$userId");

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

    public function test_can_get_users_in_game()
    {
        $user = User::factory()->create();
        $inviteKey = InviteKey::where('value', $user->invite_key)->first();

        $response = $this->get("/api/game/$inviteKey->game_id/users");

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
