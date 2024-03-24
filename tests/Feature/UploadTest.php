<?php

use App\Models\Device;
use App\Models\Upload;
use App\Models\User;
use App\Policies\UploadPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->device = Device::factory()->create(['user_id' => $this->user->id]);
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->policy = new UploadPolicy();
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

test('set uploads requirements', function () {
    Passport::actingAs($this->admin);

    $date = \Carbon\Carbon::now()->addMonth()->toDateString();

    $response = $this->postJson(route('upload.requirements', $this->user), [
       'max_upload' => 2,
       'max_tries' => 2,
       'expiration_date' => $date
    ]);

    $response
        ->assertStatus(200)
        ->assertJson(['message' => 'Requirements set successfully']);

    $this->user->refresh();
    $this->device->refresh();

    $this->assertEquals($this->user->max_upload, 2);
    $this->assertEquals($this->user->max_tries, 2);
    $this->assertEquals($this->device->expiration_date, \Carbon\Carbon::parse($date));
});


test('allows users upload to be viewable', function () {
    Passport::actingAs($this->admin);

    $upload = Upload::factory()->create(['user_id' => $this->user->id, 'device_id' => $this->device->id]);

    $this->assertEquals($this->device->disabled, true);

    $response = $this->postJson(route('upload.enable', $upload));

    $response
        ->assertStatus(200)
        ->assertJson(['message' => 'Device status updated']);

    $this->device->refresh();

    $this->assertEquals($this->device->disabled, false);
});

test('fetch all images based on specific conditions', function () {
    Passport::actingAs($this->admin);

    $user = User::factory()->create(['max_upload' => 2, 'max_tries' => 2, 'upload_count' => 0]);
    $user2 = User::factory()->create(['max_upload' => 2, 'max_tries' => 2, 'upload_count' => 0]);

    $device = Device::factory()->create(['user_id' => $user->id, 'expiration_date' => \Carbon\Carbon::now()->addDay(), 'disabled' => false]);
    $device2 = Device::factory()->create(['user_id' => $user2->id, 'expiration_date' => \Carbon\Carbon::now()->subWeek()]);

    Upload::factory(2)->create(['user_id' => $user->id, 'device_id' => $device->id]);
    Upload::factory(1)->create(['user_id' => $user2->id, 'device_id' => $device2->id]);


    $uploads = $this->withHeaders([
                        'Device' => $device->uid
                    ])
                    ->getJson(route('upload.index'));

    expect($uploads)->assertJsonCount(2);

    dd($uploads->json());
});
