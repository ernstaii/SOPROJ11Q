<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $request = [
            'username' => 'Luke',
            'location' => '51.498134,-0.201755'
        ];

        $response = $this->post('api/users', $request);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', $request);
    }
}
