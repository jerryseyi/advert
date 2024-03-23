<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UploadFactory extends Factory
{
    protected $model = Upload::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'device_id' => Device::factory()->create()->id,
            'image' => $this->faker->word(),
            'size' => $this->faker->word(),
            'type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
