<?php

namespace Database\Factories;

use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReturnItemFactory extends Factory
{
    protected $model = ReturnItem::class;

    public function definition(): array
    {
        return [
            'return_id' => ReturnRequest::query()->inRandomOrder()->value('id') ?? ReturnRequest::factory(),
            'order_item_id' => OrderItem::query()->inRandomOrder()->value('id') ?? OrderItem::factory(),
            'quantity' => $this->faker->numberBetween(1, 3),
            'condition_note' => $this->faker->optional()->sentence(),
            'refund_amount' => $this->faker->randomFloat(2, 0, 500),
        ];
    }
}
