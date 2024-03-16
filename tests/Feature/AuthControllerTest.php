<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('users can login with valid credentials', function () {
   $user = User::factory()->create([
       'email' => 'john@doe.com',
       'password' => bcrypt('password')
   ]);

   $response = $this->postJson('/api/login', [
      'email' => 'john@doe.com',
      'password' => 'password'
   ]);

   $response
       ->assertStatus(200)
       ->assertJsonStructure(['user' => 'access_token']);
});
