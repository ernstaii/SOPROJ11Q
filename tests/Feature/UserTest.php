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

    public function test_can_get_user()
    {
        $user = User::factory()->create();
        $user->refresh();

        $this->get('api/users/' . $user->id)
            ->assertStatus(200)
            ->assertExactJson($user->toArray());
    }

    public function test_can_store_user()
    {
        $game = Game::factory()->create();
        $invite_key = InviteKey::factory()->state([
            'game_id' => $game->id
        ])->create();

        $this->post('api/users/', [
            'username' => 'user',
            'location' => '51.763010,5.426781',
            'invite_key' => $invite_key->value
        ])->assertStatus(201);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_can_update_user_location()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id
        ])->create();

        $response = $this->patch('api/users/' . $user->id, [
            'location' => '51.763010,5.426781'
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', $response->getVary());
    }

    public function test_cannot_store_user_with_duplicate_key()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        $invite_key = InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id
        ])->create();

        $this->post('api/users/', [
            'username' => 'user',
            'location' => '51.763010,5.426781',
            'invite_key' => $invite_key->value
        ])->assertStatus(422)
            ->isInvalid();

        $this->assertDatabaseCount('users', 1);
    }
}
