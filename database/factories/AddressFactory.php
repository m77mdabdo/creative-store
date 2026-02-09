<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'label' => $this->faker->optional()->randomElement(['Home','Office']),
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->numerify('05########'),
            'country' => 'Saudi Arabia',
            'city' => $this->faker->randomElement(['Riyadh','Jeddah','Dammam']),
            'district' => $this->faker->optional()->word(),
            'street' => $this->faker->streetName(),
            'building_no' => $this->faker->optional()->numerify('##'),
            'apartment_no' => $this->faker->optional()->numerify('##'),
            'postal_code' => $this->faker->optional()->postcode(),
            'lat' => $this->faker->optional()->latitude(16, 32),
            'lng' => $this->faker->optional()->longitude(34, 56),
            'notes' => $this->faker->optional()->sentence(),
            'is_default' => $this->faker->boolean(30),
        ];
    }
}
