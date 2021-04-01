<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_user()
    {
        $user = User::factory()->make();

        $response = $this->call('POST', '/api/users', [
            'username'   => $user->getAttribute('username'),
            'location'   => $user->getAttribute('location'),
            'role'       => $user->getAttribute('role'),
            'invite_key' => $user->getAttribute('invite_key'),
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'username'   => $user->getAttribute('username'),
            'location'   => $user->getAttribute('location'),
            'role'       => $user->getAttribute('role'),
            'invite_key' => $user->getAttribute('invite_key'),
        ]);
    }

    /** @test */
    public function update_location()
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

    /** @test */
    public function get_user()
    {
        $user = User::factory()->create();
        $userId = $user->getKey();

        $response = $this->call('GET', "/api/users/$userId");

        $response->assertStatus(Response::HTTP_OK)->assertExactJson([
            'id'         => $userId,
            'username'   => $user->username,
            'location'   => $user->location,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'invite_key' => $user->invite_key,
            'role'       => $user->role,
        ]);
    }
}
