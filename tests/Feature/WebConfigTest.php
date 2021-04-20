<?php

namespace Tests\Feature;

use App\Enums\Statuses;
use App\Events\StartGameEvent;
use App\Models\InviteKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class WebConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_show_config_screen()
    {
        $game = Game::factory()->create();

        $this->get('/games/' . $game->id)
            ->assertStatus(200)
            ->assertViewIs('config.main')
            ->assertViewHas(['police_keys', 'thief_keys', 'id']);
    }

    public function test_can_start_game()
    {
        Event::fake();

        $game = Game::factory()->create();
        InviteKey::factory()->count(3)->state([
            'game_id' => $game->id
        ])->create();

        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302)
            ->assertRedirect('/games/' . $game->id);

        $game->refresh();
        $this->assertEquals(Statuses::Ongoing, $game->status);
        Event::assertDispatched(StartGameEvent::class);
    }

    public function test_cannot_start_game_without_keys()
    {
        $game = Game::factory()->create();

        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302)
            ->isInvalid();

        $game->refresh();
        $this->assertEquals(Statuses::Config, $game->status);
    }

    public function test_cannot_update_state_if_game_is_in_invalid_state()
    {
        $game = Game::factory()->create();

        // ==================== Config -> Finished ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Finished
        ])->assertStatus(302)
            ->isInvalid();

        // ==================== Config -> Paused ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Paused
        ])->assertStatus(302)
            ->isInvalid();

        $game->refresh();
        $this->assertEquals(Statuses::Config, $game->status);
    }
}