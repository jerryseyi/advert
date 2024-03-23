<?php

use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $this->policy = new \App\Policies\UploadPolicy();
});

test('users can upload image', function () {

    Storage::fake('public');

    \Laravel\Passport\Passport::actingAs($this->admin);
    $id = $this->admin->id;

    $file = UploadedFile::fake()->image('test.jpg');
    $imageName = time().'.'.$file->getClientOriginalExtension();


    $response = $this->postJson("/api/${id}/uploads", [
        'image' => $file
    ]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Upload Successful.'
        ]);

    // Assert that file was stored in disk.
//    Storage::disk('public')->assertExists('public/uploads/' .$imageName);
});

test('a user can update an existing image', function () {

    \Laravel\Passport\Passport::actingAs($this->user);

    // use fake storage for testing.
    Storage::fake('public');

    $upload = Upload::factory()->create([
        'user_id' => $this->user->id,
        'image' => 'test.jpg',
    ]);

    $newImage = UploadedFile::fake()->image('another.jpg');

    $response = $this->patchJson(route('upload.update', [$this->user, $upload]), [
       'image' => $newImage
    ]);

    $response
        ->assertStatus(200)
        ->assertJson(['message' => 'Updated Successfully']);

//    $this->assertEquals('storage/uploads/' . $newImage->hashName(), $upload->refresh()->image);
});
