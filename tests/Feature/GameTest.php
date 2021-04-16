<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Loot;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this->call('GET', "/api/game/$game->id/loot");

        $response->assertStatus(200);
        $this->assertCount(1, (array) $response->getContent());
    }
}
