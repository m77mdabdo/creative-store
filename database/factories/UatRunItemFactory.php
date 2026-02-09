<?php

namespace Database\Factories;

use App\Models\UatRunItem;
use App\Models\UatRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class UatRunItemFactory extends Factory
{
    protected $model = UatRunItem::class;

    public function definition(): array
    {
        return [
            'uat_run_id' => UatRun::query()->inRandomOrder()->value('id') ?? UatRun::factory(),
            'scenario_code' => strtoupper($this->faker->bothify('SCN-###-??')),
            'title' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(['pending','passed','failed']),
            'evidence' => $this->faker->optional()->sentence(),
        ];
    }
}
