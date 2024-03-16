<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('users with valid credentials can login', function () {

    User::factory()->create(['email' => 'john@doe.com']);

    $response = $this->postJson('/api/login', [
        'email' => 'john@doe.com',
        'password' => 'password',
    ]);

    $response
        ->assertStatus(200)
        ->assertJsonStructure(['user', 'access_token']);
});

test('users with invalid credentials cannot login', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'error@doe.com',
        'password' => 'password'
    ]);

    $response
        ->assertStatus(422)
        ->assertJson(['message' => 'Invalid Credentials']);
});
