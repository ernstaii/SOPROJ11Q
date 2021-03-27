<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_user()
    {
        $request = [
            'username' => 'Luke',
            'location' => '51.498134,-0.201755'
        ];

        $response = $this->post('api/users', $request);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', $request);
    }

    /** @test */
    public function update_location()
    {
        $request = [
            'location' => '51.498134,-0.201755'
        ];

        $response = $this->put('api/users/1', $request);
        $response->assertStatus(200);
        $this->assertEquals("51.498134,-0.201754", User::find(1)->pluck('location'));
    }
}
