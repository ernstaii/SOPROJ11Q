<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Enums\UserStatuses;
use App\Models\BorderMarker;
use App\Models\Game;
use App\Models\InviteKey;
use App\Models\Loot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameAPITest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_game()
    {
        $game = Game::factory()->create();
        $game->refresh();

        $this->get('/api/games/' . $game->id)
            ->assertStatus(200)
            ->assertExactJson($game->toArray());
    }

    public function test_can_get_key()
    {
        $game = Game::factory()->create();
        $key = InviteKey::factory()->state([
            'game_id' => $game->id,
        ])->create();
        $key->refresh();

        $this->get('/api/invite-keys/' . $key->value)
            ->assertStatus(200)
            ->assertExactJson($key->toArray());
    }

    public function test_cannot_get_key_if_game_is_finished()
    {
        $game = Game::factory()->finished()->create();
        $key = InviteKey::factory()->state([
            'game_id' => $game->id,
        ])->create();
        $key->refresh();

        $this->get('/api/invite-keys/' . $key->value)
            ->assertStatus(422)
            ->isInvalid();
    }

    public function test_can_get_users_attached_to_game()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ])->create();

        // ==================== Without Role ====================
        $this->get('/api/games/' . $game->id . '/users')
            ->assertStatus(200)
            ->assertJsonCount(1);

        // ==================== With Role ====================
        $res = $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount(1);

        $content_array = (array) json_decode($res->getContent());
        $this->assertObjectHasAttribute('role', $content_array[0]);
    }

    public function test_users_with_role_only_returns_keys_with_user()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ])->create();
        InviteKey::factory(4)->state([
            'game_id' => $game->id,
        ])->create();

        $res = $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount(1);

        $content_array = (array) json_decode($res->getContent());
        $this->assertObjectHasAttribute('role', $content_array[0]);
    }

    public function test_can_get_loot_attached_to_game()
    {
        $game = Game::factory()->create();
        Loot::factory()->state([
            'lootable_id'   => $game->id,
            'lootable_type' => Game::class,
        ])->create();

        $this->get('/api/games/' . $game->id . '/loot')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_can_get_border_markers_of_game()
    {
        $game = Game::factory()->create();
        BorderMarker::factory()
            ->state(['borderable_id' => $game->id, 'borderable_type' => Game::class])
            ->isFirstMarker()
            ->create();
        BorderMarker::factory()
            ->state(['borderable_id' => $game->id, 'borderable_type' => Game::class])
            ->isSecondMarker()
            ->create();
        BorderMarker::factory()
            ->state(['borderable_id' => $game->id, 'borderable_type' => Game::class])
            ->isThirdMarker()
            ->create();
        BorderMarker::factory()
            ->state(['borderable_id' => $game->id, 'borderable_type' => Game::class])
            ->isFourthMarker()
            ->create();
        BorderMarker::factory()
            ->state(['borderable_id' => $game->id, 'borderable_type' => Game::class])
            ->isFifthMarker()
            ->create();

        $this->get('/api/games/' . $game->id . '/border-markers')
            ->assertStatus(200)
            ->assertJsonCount(5);

        $this->assertDatabaseCount('border_markers', 5);
    }

    public function test_cannot_get_used_key()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        $key = InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ])->create();
        $key->refresh();

        $this->get('/api/invite-keys/' . $key->value)
            ->assertStatus(422)
            ->isInvalid();
    }

    public function test_can_catch_thief()
    {
        $game = Game::factory()->create();
        $user = User::factory()->playing()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'role'    => Roles::Thief,
        ])->create();

        $this->patch('/api/users/' . $user->id . '/catch')
            ->assertStatus(200);

        $user->refresh();
        $this->assertEquals(UserStatuses::Caught, $user->status);
    }

    public function test_cannot_catch_thief_not_playing()
    {
        $game = Game::factory()->create();
        $user = User::factory()->state([
            'status' => UserStatuses::InLobby,
        ])->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'role'    => Roles::Thief,
        ])->create();

        $this->patch('/api/users/' . $user->id . '/catch')
            ->assertStatus(422)
            ->isInvalid();

        $user->refresh();
        $this->assertEquals(UserStatuses::InLobby, $user->status);
    }

    public function test_can_request_new_keys_for_game()
    {
        $game = Game::factory()->create();

        $this->post('/games/' . $game->id . '/invite-keys', [
            'input' => 10,
            'ratio' => 25,
        ])->assertStatus(200)
            ->assertJsonCount(10);
        $this->assertDatabaseCount('invite_keys', 10);
    }

    public function test_cannot_request_new_keys_if_game_already_has_keys()
    {
        $game = Game::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
        ])->create();

        $this->post('/games/' . $game->id . '/invite-keys', [
            'input' => 10,
            'ratio' => 25,
        ])->assertStatus(422)
            ->isInvalid();

        $this->assertDatabaseCount('invite_keys', 1);
    }

    public function test_requesting_keys_with_invalid_request_will_not_redirect()
    {
        $game = Game::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
        ])->create();

        $this->post('/games/' . $game->id . '/invite-keys', [
            'input' => 10,
        ])->assertStatus(422)
            ->isInvalid();
    }

    public function test_requesting_unknown_resource_returns_404()
    {
        $this->get('/api/invite-keys/AAAA')
            ->assertStatus(404);

        $this->patch('/api/users/1/catch')
            ->assertStatus(404);
    }

    public function test_update_police_score()
    {
        $game = Game::factory()->create();
        $currentScore = $game->getAttribute('police_score');
        $scoreIncreaseValue = 40;

        $response = $this->patch('/api/games/' . $game->id . '/police-score/' . $scoreIncreaseValue)
            ->assertStatus(200)
            ->content();

        $this->assertTrue(is_int((int) $response));
        $this->assertEquals(($currentScore += $scoreIncreaseValue), intval($response));

        $response = $this->patch('/api/games/' . $game->id . '/police-score/' . $scoreIncreaseValue)
            ->assertStatus(200)
            ->content();

        $this->assertTrue(is_int((int) $response));
        $this->assertEquals(($currentScore + $scoreIncreaseValue), intval($response));
    }

    public function test_update_thief_score()
    {
        $game = Game::factory()->create();
        $currentScore = $game->getAttribute('police_score');
        $scoreIncreaseValue = 40;

        $response = $this->patch('/api/games/' . $game->id . '/thieves-score/' . $scoreIncreaseValue)
            ->assertStatus(200)
            ->content();

        $this->assertTrue(is_int((int) $response));
        $this->assertEquals(($currentScore += $scoreIncreaseValue), intval($response));

        $response = $this->patch('/api/games/' . $game->id . '/thieves-score/' . $scoreIncreaseValue)
            ->assertStatus(200)
            ->content();

        $this->assertTrue(is_int((int) $response));
        $this->assertEquals(($currentScore + $scoreIncreaseValue), intval($response));
    }
}
