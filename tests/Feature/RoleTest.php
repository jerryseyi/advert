<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

test('', function () {
    $user = User::factory()->create();

    $user->assignRole('admin')->givePermissionTo('access all');

    $this->assertTrue($user->hasRole('admin'));
});
