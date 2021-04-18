<?php

namespace Tests\Feature;

use App\Enums\Statuses;
use App\Models\Game;
use App\Models\InviteKey;
use App\Models\Loot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OutOfBoundsException;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_loot()
    {
        $game = Game::create();
        $loot = Loot::create([
            'name' => 'Loot',
            'location' => 51.756789 . "," . 5.532817
        ]);
        $game->loots()->attach($loot);

        $response = $this->get("/api/game/$game->id/loot");

        $response->assertStatus(200);
        $this->assertCount(1, (array)$response->getContent());
    }

    public function test_can_get_status_of_game()
    {
        $game = Game::create();

        $response = $this->get('/api/game/' . $game->id . '/status')
            ->assertStatus(200);

        $game->refresh();
        $this->assertEquals($game->status, $response->getContent());
    }

    public function test_can_start_game()
    {
        $game = Game::create();
        InviteKey::create([
            'value' => 'A000',
            'game_id' => $game->id
        ]);

        $this->put('/games/' . $game->id, [
            'duration' => 120,
            'interval' => 30,
            'state' => Statuses::Ongoing
        ])->assertStatus(302);

        $game->refresh();
        $this->assertEquals(Statuses::Ongoing, $game->status);
    }

    public function test_can_pause_game()
    {
        $game = Game::create([
            'status' => Statuses::Ongoing,
            'duration' => 120,
            'interval' => 30,
            'time_left' => 7200
        ]);
        InviteKey::create([
            'value' => 'A000',
            'game_id' => $game->id
        ]);

        $this->put('/games/' . $game->id, [
            'state' => Statuses::Paused
        ])->assertStatus(302);

        $game->refresh();
        $this->assertEquals(Statuses::Paused, $game->status);
    }

    public function test_can_resume_game()
    {
        $game = Game::create([
            'status' => Statuses::Paused,
            'duration' => 120,
            'interval' => 30,
            'time_left' => 7200
        ]);
        InviteKey::create([
            'value' => 'A000',
            'game_id' => $game->id
        ]);

        $this->put('/games/' . $game->id, [
            'state' => Statuses::Ongoing
        ])->assertStatus(302);

        $game->refresh();
        $this->assertEquals(Statuses::Ongoing, $game->status);
    }

    public function test_can_finish_game()
    {
        $game = Game::create([
            'status' => Statuses::Paused,
            'duration' => 120,
            'interval' => 30,
            'time_left' => 7200
        ]);
        InviteKey::create([
            'value' => 'A000',
            'game_id' => $game->id
        ]);

        $this->put('/games/' . $game->id, [
            'state' => Statuses::Finished
        ])->assertStatus(302);

        $game->refresh();
        $this->assertEquals(Statuses::Finished, $game->status);
    }

    public function test_cannot_finish_config_game()
    {
        $game = Game::create([
            'status' => Statuses::Config,
            'duration' => 120,
            'interval' => 30,
            'time_left' => 7200
        ]);
        InviteKey::create([
            'value' => 'A000',
            'game_id' => $game->id
        ]);

        $this->put('/games/' . $game->id, [
            'state' => Statuses::Finished
        ])->assertStatus(302);

        $game->refresh();
        $this->assertEquals(Statuses::Config, $game->status);
    }

    public function test_cannot_update_game_state_of_game_without_keys()
    {
        $game = Game::create([
            'status' => Statuses::Config
        ]);

        $this->put('/games/' . $game->id, [
            'duration' => 120,
            'interval' => 30,
            'state' => Statuses::Ongoing
        ])->assertStatus(302);

        $game->refresh();
        $this->assertEquals(Statuses::Config, $game->status);
    }

    public function test_updating_game_state_without_state_throws_error()
    {
        $game = Game::create([
            'status' => Statuses::Config
        ]);

        $response = $this->put('/games/' . $game->id, [
            'duration' => 120,
            'interval' => 30,
            'state' => Statuses::None
        ]);
        $this->assertEquals("De opgegeven status is niet bekend.", $response->exception->getMessage());

        $game->refresh();
        $this->assertEquals(Statuses::Config, $game->status);
    }
}
