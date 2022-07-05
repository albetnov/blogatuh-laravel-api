<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_exists()
    {
        $this->seed(UserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@mail.com'
        ]);
    }

    public function test_login_success()
    {
        $this->seed(UserSeeder::class);
        $this->withHeader("Accept", "application/json")->postJson("/api/login", [
            'email' => 'admin@mail.com',
            'password' => 'admin123'
        ])->assertStatus(200);
    }

    public function test_login_failed()
    {
        $this->withHeader("Accept", "application/json")->postJson("/api/login", [
            'email' => 'wrong@cresidentials.com',
            'password' => 'wrong123'
        ])->assertStatus(401);
    }
}
