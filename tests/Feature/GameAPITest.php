<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Loot;
use App\Models\User;
use App\Models\InviteKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class GameAPITest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_game()
    {
        $game = Game::factory()->create();

        $this->get('/api/games/' . $game->id)
            ->assertStatus(200)
            ->assertExactJson($game->toArray());
    }

    public function test_can_get_key()
    {
        $game = Game::factory()->create();
        $key = InviteKey::factory()->state([
            'game_id' => $game->id
        ])->create();
        $key->refresh();

        $this->get('/api/invite-keys/' . $key->value)
            ->assertStatus(200)
            ->assertExactJson($key->toArray());
    }

    public function test_can_get_users_attached_to_game()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id
        ])->create();

        $this->get('/api/games/' . $game->id . '/users')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_can_get_loot_attached_to_game()
    {
        $game = Game::factory()->create();
        $loot_item = Loot::factory()->create();
        $game->loot()->attach($loot_item->id);

        $this->get('/api/games/' . $game->id . '/loot')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_can_request_new_keys_for_game()
    {
        $game = Game::factory()->create();

        $this->post('/games/' . $game->id . '/invite-keys', [
            'input' => 10,
            'ratio' => 25
        ])->assertStatus(200)
            ->assertJsonCount(10);

        $this->assertDatabaseCount('invite_keys', 10);
    }

    public function test_cannot_request_new_keys_if_game_already_has_keys()
    {
        $game = Game::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id
        ])->create();

        $this->post('/games/' . $game->id . '/invite-keys', [
            'input' => 10,
            'ratio' => 25
        ])->assertStatus(302)
            ->isInvalid();

        $this->assertDatabaseCount('invite_keys', 1);
    }

    public function test_requesting_unknown_key_gives_404()
    {
        $this->get('/api/invite-keys/AAAA')
            ->assertStatus(404);
    }

    public function test_cannot_get_key_used_key()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        $key = InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id
        ])->create();

        $this->get('/api/invite-keys/' . $key->value)
            ->assertStatus(302)
            ->isInvalid();
    }
}