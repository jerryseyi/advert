<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

test('users can upload image', function () {
    Storage::fake('public');
    $user = \App\Models\User::factory()->create()->id;
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson("/${user}/uploads", [
        'image' => $file
    ]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Upload Successful.'
        ]);

    // Assert that file was stored in disk.
    Storage::disk('public')->assertExists('uploads/' .$file->hashName());
});
