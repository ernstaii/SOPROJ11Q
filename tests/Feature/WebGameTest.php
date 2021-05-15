<?php

namespace Tests\Feature;

use App\Enums\Statuses;
use App\Events\EndGameEvent;
use App\Events\PauseGameEvent;
use App\Events\ResumeGameEvent;
use App\Models\BorderMarker;
use App\Models\Game;
use App\Models\InviteKey;
use App\Models\Loot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class WebGameTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_to_game_index()
    {
        $this->get('/')
            ->assertStatus(302)
            ->assertRedirect('/games');

        $this->get('/games')
            ->assertStatus(200)
            ->assertViewHas('games');
    }

    public function test_can_store_game()
    {
        $this->post('/games')
            ->assertStatus(302)
            ->assertRedirect('/games/'. Game::first()->id);

        $this->assertDatabaseCount('games', 1);
    }

    public function test_can_show_game_screen()
    {
        $game = Game::factory()->ongoing()->create();

        $this->get('/games/' . $game->id)
            ->assertStatus(200)
            ->assertViewIs('game.main')
            ->assertViewHas(['id', 'users']);
    }

    public function test_can_update_state_if_game_is_in_valid_state()
    {
        Event::fake();

        $game = Game::factory()->ongoing()->create();
        InviteKey::factory()->count(3)->state([
            'game_id' => $game->id
        ])->create();
        BorderMarker::factory()->count(3)->state([
            'borderable_id' => $game->id,
            'borderable_type' => Game::class
        ])->create();

        // ==================== Ongoing -> Finished ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Finished
        ])->assertStatus(302)
            ->assertRedirect('/games/' . $game->id);

        Event::assertDispatchedTimes(EndGameEvent::class);
        $game->refresh();
        $this->assertEquals(Statuses::Finished, $game->status);

        $game->status = Statuses::Ongoing;
        $game->save();
        // ==================== Ongoing -> Paused ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Paused
        ])->assertStatus(302);

        Event::assertDispatchedTimes(PauseGameEvent::class);
        $game->refresh();
        $this->assertEquals(Statuses::Paused, $game->status);

        // ==================== Paused -> Ongoing ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302);

        Event::assertDispatchedTimes(ResumeGameEvent::class);
        $game->refresh();
        $this->assertEquals(Statuses::Ongoing, $game->status);

        $game->status = Statuses::Paused;
        $game->save();
        // ==================== Paused -> Finished ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Finished
        ])->assertStatus(302);

        Event::assertDispatchedTimes(EndGameEvent::class, 2);
        $game->refresh();
        $this->assertEquals(Statuses::Finished, $game->status);
    }

    public function test_cannot_update_state_if_game_is_in_invalid_state()
    {
        $game = Game::factory()->finished()->create();

        // ==================== Finished -> Config ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Config
        ])->assertStatus(302)
            ->isInvalid();

        // ==================== Finished -> Ongoing ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Ongoing
        ])->assertStatus(302)
            ->isInvalid();

        // ==================== Finished -> Paused ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Paused
        ])->assertStatus(302)
            ->isInvalid();

        $game->refresh();
        $this->assertEquals(Statuses::Finished, $game->status);

        $game->status = Statuses::Ongoing;
        $game->save();
        // ==================== Ongoing -> Config ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Config
        ])->assertStatus(302)
            ->isInvalid();

        // ==================== Ongoing -> Something invalid ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::None
        ])->assertStatus(302)
            ->isInvalid();

        $game->refresh();
        $this->assertEquals(Statuses::Ongoing, $game->status);

        $game->status = Statuses::Paused;
        $game->save();
        // ==================== Paused -> Config ====================
        $this->put('/games/' . $game->id, [
            'duration' => '120',
            'interval' => '30',
            'state' => Statuses::Config
        ])->assertStatus(302)
            ->isInvalid();

        $game->refresh();
        $this->assertEquals(Statuses::Paused, $game->status);
    }

    public function test_can_destroy_game()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        InviteKey::factory()->state([
            'game_id' => $game->id,
            'user_id' => $user->id
        ])->create();
        Loot::factory()->state([
            'lootable_id' => $game->id,
            'lootable_type' => Game::class
        ])->create();

        $this->delete('/games/' . $game->id)
            ->assertStatus(302)
            ->assertRedirect('/games');

        $this->assertDatabaseCount('games', 0);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('invite_keys', 0);
        $this->assertDatabaseCount('loot', 0);
        $this->assertDatabaseCount('border_markers', 0);
    }
}
