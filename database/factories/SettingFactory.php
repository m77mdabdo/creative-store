<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->slug(2),
            'value' => $this->faker->optional()->sentence(),
            'type' => $this->faker->randomElement(['string','text','json','bool','int']),
        ];
    }
}
