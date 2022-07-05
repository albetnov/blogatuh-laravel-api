<?php

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\seed;
use function Pest\Laravel\withHeader;

uses(RefreshDatabase::class);

test("User Exists", function () {
    seed(UserSeeder::class);
    assertDatabaseHas('users', [
        'email' => 'admin@mail.com'
    ]);
});

test("Login Success", function () {
    seed(UserSeeder::class);
    withHeader("Accept", "application/json")->postJson("/api/login", [
        'email' => 'admin@mail.com',
        'password' => 'admin123'
    ])->assertStatus(200);
});

test("Login Failed", function () {
    withHeader("Accept", "application/json")->postJson("/api/login", [
        'email' => 'wrong@cresidentials.com',
        'password' => 'wrong123'
    ])->assertStatus(401);
});
