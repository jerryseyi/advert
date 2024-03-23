<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

beforeEach(function () {
   $this->user = \App\Models\User::factory()->create();
});

test('it disconnect a device', function () {
    $this->withoutExceptionHandling();

    \Laravel\Passport\Passport::actingAs($this->user);
    $device = \App\Models\Device::factory()->create(['user_id' => $this->user->id]);
    $response = $this->postJson(route('device.disconnect', $device));

    $response
        ->assertStatus(200)
        ->assertJson(['message' => 'device disconnected successfully']);

    $device->refresh();

    $this->assertDatabaseHas('devices', [
       'disabled' => true
    ]);
});

