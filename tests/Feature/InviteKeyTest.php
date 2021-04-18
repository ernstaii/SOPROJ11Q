<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\InviteKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_unused_invite_key()
    {
        $game = Game::create();
        $invite_key = InviteKey::create([
            'value' => 'AA00',
            'game_id' => $game->id
        ]);

        $response = $this->get('/api/invite-key/' . $invite_key->value);

        $response->assertStatus(200);
        $this->assertCount(1, (array) $response->getContent());
    }

    public function test_get_used_invite_key()
    {
        $game = Game::create();
        $invite_key = InviteKey::create([
            'value' => 'AA00',
            'game_id' => $game->id
        ]);
        $user = User::create([
            'username' => 'test_user',
            'location' => '51.498134,-0.201754',
            'invite_key' => 'AA00'
        ]);

        $response = $this->get('/api/invite-key/' . $invite_key->value);

        $response->assertStatus(403);
    }

    public function test_get_non_existing_invite_key()
    {
        $response = $this->get('/api/invite-key/AA00');

        $response->assertStatus(404);
    }
}
