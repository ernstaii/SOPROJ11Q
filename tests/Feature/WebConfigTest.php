<?php

namespace Tests\Feature;

use App\Enums\Statuses;
use App\Events\StartGameEvent;
use App\Models\BorderMarker;
use App\Models\InviteKey;
use App\Models\Loot;
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
        BorderMarker::factory()->count(3)->state([
            'game_id' => $game->id
        ])->create();
        $loot = array();
        array_push($loot, Loot::factory()->count(4)->create());
        foreach ($loot as $loot_item) {
            $game->loot()->attach($loot_item);
        }

        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'jail_time' => '20',
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

    public function test_can_store_border_markers()
    {
        $game = Game::factory()->create();

        $this->post('/games/' . $game->id . '/border-markers', [
            'lats' => [
                51.733201,
                51.733725,
                51.790284
            ],
            'lngs' => [
                5.480463,
                5.587817,
                5.577451
            ]
        ])->assertStatus(200);

        $this->assertDatabaseCount('border_markers', 3);
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
