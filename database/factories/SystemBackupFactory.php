<?php

namespace Database\Factories;

use App\Models\SystemBackup;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemBackupFactory extends Factory
{
    protected $model = SystemBackup::class;

    public function definition(): array
    {
        $started = now()->subMinutes($this->faker->numberBetween(10, 2000));
        return [
            'type' => $this->faker->randomElement(['manual','scheduled']),
            'file_path' => 'backups/'.$this->faker->uuid().'.zip',
            'size_bytes' => $this->faker->optional()->numberBetween(10_000, 500_000_000),
            'status' => $this->faker->randomElement(['success','failed']),
            'started_at' => $started,
            'finished_at' => (clone $started)->addMinutes($this->faker->numberBetween(1, 30)),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
