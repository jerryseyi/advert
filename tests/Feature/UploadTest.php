<?php

use App\Models\Device;
use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $this->policy = new \App\Policies\UploadPolicy();
});

test('users can upload image', function () {

    Storage::fake('public');

    Passport::actingAs($this->admin);
    $id = $this->admin->id;

    Device::factory()->create(['user_id' => $this->admin->id]);

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

    Passport::actingAs($this->user);

    // use fake storage for testing.
    Storage::fake('public');
    $device = Device::factory()->create([
       'user_id' => $this->user->id,
    ]);

    $upload = Upload::factory()->create([
        'user_id' => $this->user->id,
        'device_id' => $device->id,
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

test('it can exclude an image from a device', function () {
    Storage::fake('public');

    Passport::actingAs($this->admin);

    $device = Device::factory()->create(['user_id' => $this->user->id]);

    $upload = Upload::factory()->create([
        'user_id' => $this->user->id,
        'device_id' => $device->id,
        'image' => 'test.jpg',
    ]);

    $response = $this->postJson(route('exclude-upload.store', [$device, $upload]));

    $response
        ->assertStatus(200)
        ->assertJson(['message' => 'image excluded successfully']);

    $device->refresh();

    expect($device->upload_ids)->toContain($upload->id);

});
