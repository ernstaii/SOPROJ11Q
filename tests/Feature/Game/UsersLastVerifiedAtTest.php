<?php

namespace Tests\Feature\Game;

use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Models\Game;
use App\Models\InviteKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersLastVerifiedAtTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_users_that_have_not_been_verified()
    {
        $game = Game::factory()->state(['last_interval_at' => Carbon::now()])->create();
        $user = User::factory()->state(['last_verified_at' => Carbon::now()->addSeconds(-60)])->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ])->create();

        $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function test_can_get_users_that_have_been_verified()
    {
        $game = Game::factory()->state(['last_interval_at' => Carbon::now()])->create();
        $user = User::factory()->state(['last_verified_at' => Carbon::now()->addSeconds(5)])->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ])->create();

        // ==================== User verification hasn't been up to date, so there are no results ====================
        $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_if_status_of_player_is_playing()
    {
        $game = Game::factory()->state(['last_interval_at' => Carbon::now(), 'status' => Statuses::Ongoing])->create();
        $user = User::factory()->state([
            'last_verified_at' => Carbon::now()->addSeconds(-60),
            'status' => UserStatuses::Playing,
        ])->create();
        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user->id])->create();

        $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount(0);

        $status = UserStatuses::Disconnected;
        $this->test_status_of_user($user->id, $status);

        $this->patch('api/users/' . $user->id, ['location' => '51.763010,5.426781',])
            ->assertStatus(200);

        $status = UserStatuses::Playing;
        $this->test_status_of_user($user->id, $status);

        $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    protected function test_status_of_user($userId, $status)
    {
        $response = $this->get('api/users/' . $userId);

        $response->assertStatus(200);
        $this->assertEquals($userId, $response["id"]);
        $this->assertEquals($status, $response["status"]);
    }

    public function test_if_status_of_player_is_disconnected()
    {
        $game = Game::factory()->state(['last_interval_at' => Carbon::now(), 'status' => Statuses::Ongoing])->create();
        $user = User::factory()->state([
            'last_verified_at' => Carbon::now()->addSeconds(-60),
            'status' => UserStatuses::Playing,
        ])->create();
        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user->id])->create();

        $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount(0);

        $status = UserStatuses::Disconnected;
        $this->test_status_of_user($user->id, $status);
    }

    public function test_get_multiple_users_with_mixed_last_verified_at_verified()
    {
        $game = Game::factory()->state(['last_interval_at' => Carbon::now()])->create();

        // Create multiple users for testing te result
        $user = User::factory()->state(['last_verified_at' => Carbon::now()->addSeconds(-60)])->create();
        $user2 = User::factory()->state(['last_verified_at' => Carbon::now()->addSeconds(-120)])->create();
        $user3 = User::factory()->state(['last_verified_at' => Carbon::now()])->create();
        $user4 = User::factory()->state(['last_verified_at' => Carbon::now()->addSeconds(5)])->create();
        $user5 = User::factory()->state(['last_verified_at' => Carbon::now()->addSeconds(10)])->create();
        $user6 = User::factory()->state(['last_verified_at' => Carbon::now()->addSeconds(15)])->create();

        // 4 out of 6 users are verified correctly
        $correctVerifiedUsersCount = 4;

        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user->id])->create();
        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user2->id])->create();
        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user3->id])->create();
        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user4->id])->create();
        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user5->id])->create();
        InviteKey::factory()->state(['game_id' => $game->id, 'user_id' => $user6->id])->create();

        // Check result
        $this->get('/api/games/' . $game->id . '/users-with-role')
            ->assertStatus(200)
            ->assertJsonCount($correctVerifiedUsersCount);
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
}
