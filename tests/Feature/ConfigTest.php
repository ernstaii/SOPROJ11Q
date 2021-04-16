<?php

namespace Tests\Feature;

use App\Enums\Statuses;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_games()
    {
        Game::create();

        $this->get('/')
            ->assertStatus(200)
            ->assertViewHas('games');
    }

    public function test_can_store_game()
    {
        $this->post('/storeGame')
            ->assertStatus(302)
            ->assertLocation('/games/' . Game::all()->first()->id);
    }

    public function test_can_view_config()
    {
        $game = Game::create();

        $this->get('/games/' . $game->id)
            ->assertStatus(200)
            ->assertViewHas(['police_keys', 'thief_keys', 'id']);
    }

    public function test_can_view_game_screen()
    {
        $game = Game::create([
            'status' => Statuses::Ongoing,
            'duration' => 120,
            'interval' => 30,
            'time_left' => 7200
        ]);

        $this->get('/games/' . $game->id)
            ->assertStatus(200);
    }

    public function test_cannot_view_non_existing_game()
    {
        $this->get('/games/1')
            ->assertLocation('');
    }

    public function test_can_remove_game()
    {
        $game = Game::create();

        $this->delete('/games/' . $game->id)
            ->assertLocation('');

        $this->assertDatabaseMissing('games', $game->toArray());
    }
}
