<?php

namespace Tests\Feature;

use App\Enums\Statuses;
use App\Enums\UserStatuses;
use App\Events\StartGameEvent;
use App\Models\BorderMarker;
use App\Models\GamePreset;
use App\Models\InviteKey;
use App\Models\Loot;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class WebConfigTest extends TestCase
{
    use RefreshDatabase, InteractsWithSession;

    public function test_can_show_config_screen()
    {
        $game = Game::factory()->create();

        $this->withSession(['password' => $game->password]);

        $this->get('/games/' . $game->name)
            ->assertStatus(200)
            ->assertViewIs('config.main')
            ->assertViewHas(['police_keys', 'thief_keys', 'id']);
    }

    public function test_can_get_preset_loot()
    {
        $preset = GamePreset::factory()->create();
        Loot::factory()->count(5)->state([
            'lootable_id' => $preset->id,
            'lootable_type' => GamePreset::class
        ])->create();
        BorderMarker::factory()->count(5)->state([
            'borderable_id' => $preset->id,
            'borderable_type' => GamePreset::class
        ])->create();

        $this->get('/presets/' . $preset->id . '/loot')
            ->assertJsoncount(5);
    }

    public function test_can_clear_game_loot_and_border_markers()
    {
        $game = Game::factory()->inConfig()->create();
        Loot::factory()->count(5)->state([
            'lootable_id' => $game->id,
            'lootable_type' => Game::class
        ])->create();
        BorderMarker::factory()->count(5)->state([
            'borderable_id' => $game->id,
            'borderable_type' => Game::class
        ])->create();

        $this->assertDatabaseCount('loot', 5);
        $this->assertDatabaseCount('border_markers', 5);

        $this->delete('/games/' . $game->id . '/loot');
        $this->delete('/games/' . $game->id . '/border-markers');

        $this->assertDatabaseCount('loot', 0);
        $this->assertDatabaseCount('border_markers', 0);
    }

    public function test_can_get_preset_borders()
    {
        $preset = GamePreset::factory()->create();
        Loot::factory()->count(5)->state([
            'lootable_id' => $preset->id,
            'lootable_type' => GamePreset::class
        ])->create();
        BorderMarker::factory()->count(5)->state([
            'borderable_id' => $preset->id,
            'borderable_type' => GamePreset::class
        ])->create();

        $this->get('/presets/' . $preset->id . '/border-markers')
            ->assertJsoncount(5);
    }

    public function test_can_store_loot()
    {
        $game = Game::factory()->create();

        $this->post('/games/' . $game->id . '/loot', [
            'lats' => [
                0.000
            ],
            'lngs' => [
                0.000
            ],
            'names' => [
                'loot_item'
            ]
        ])->assertStatus(200);

        $this->assertDatabaseCount('loot', 1);
    }

    public function test_can_store_preset()
    {
        $preset = [
            'name' => 'test',
            'duration' => 30,
            'interval' => 30,
            'police_station_lat' => '0.001',
            'police_station_lng' => '0.002',
            'loot_lats' => [
                '0.001'
            ],
            'loot_lngs' => [
                '0.002'
            ],
            'loot_names' => [
                'loot_item'
            ],
            'border_lats' => [
                '0.001', '0.002', '0.003'
            ],
            'border_lngs' => [
                '0.004', '0.005', '0.006'
            ],
            'colour_theme' => '#000000'
        ];

        $this->post('/presets', $preset)
            ->assertStatus(200);

        $this->assertDatabaseCount('game_presets', 1);
    }

    public function test_can_set_police_station()
    {
        $game = Game::factory()->create();

        $this->patch('/games/' . $game->id . '/police-station', [
            'lat' => 0.001,
            'lng' => 0.002
        ]);

        $this->assertEquals('0.001,0.002', Game::first()->police_station_location);
    }

    public function test_can_start_game()
    {
        Event::fake();

        $game = Game::factory()->create();
        $users = User::factory()->count(5)->create();
        foreach ($users as $user) {
            InviteKey::factory()->state([
                'game_id' => $game->id,
                'user_id' => $user->id
            ])->create();
        }
        BorderMarker::factory()->count(3)->state([
            'borderable_id' => $game->id,
            'borderable_type' => Game::class
        ])->create();
        Loot::factory()->count(4)->state([
            'lootable_id' => $game->id,
            'lootable_type' => Game::class
        ])->create();

        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302)
            ->assertRedirect('/games/' . $game->name);

        $game->refresh();
        $this->assertEquals(Statuses::Ongoing, $game->status);
        $this->assertEquals(UserStatuses::Playing, User::first()->status);
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

    public function test_cannot_start_game_without_police_station()
    {
        $game = Game::factory()->state([
            'police_station_location' => null
        ])->create();

        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302)
            ->isInvalid();

        $this->assertEquals(Statuses::Config, Game::first()->status);
    }

    public function test_cannot_start_game_without_loot()
    {
        $game = Game::factory()->create();
        BorderMarker::factory()->count(3)->state([
            'borderable_id' => $game->id,
            'borderable_type' => Game::class
        ])->create();

        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302)
            ->isInvalid();

        $this->assertEquals(Statuses::Config, Game::first()->status);
    }

    public function test_cannot_start_game_without_borders()
    {
        $game = Game::factory()->create();
        Loot::factory()->count(3)->state([
            'lootable_id' => $game->id,
            'lootable_type' => Game::class
        ])->create();

        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302)
            ->isInvalid();

        $this->assertEquals(Statuses::Config, Game::first()->status);
    }
}
