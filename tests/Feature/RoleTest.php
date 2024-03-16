<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

test('assign user an admin role', function () {
    $user = User::factory()->create();

    $user->makeAdmin();

    $this->assertTrue($user->isAdmin());
});

test('remove admin role from user', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $user->removeAdmin();

    $this->assertNotTrue($user->isAdmin());
});
