<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 5);
        $unit = $this->faker->randomFloat(2, 10, 500);
        $taxRate = $this->faker->randomFloat(2, 0, 15);
        $taxAmount = ($unit * $qty) * ($taxRate / 100);
        $line = ($unit * $qty) + $taxAmount;

        return [
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'variant_id' => null,
            'product_name_snapshot' => $this->faker->words(3, true),
            'sku_snapshot' => $this->faker->bothify('SKU-####-??'),
            'quantity' => $qty,
            'unit_price' => $unit,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'line_total' => $line,
        ];
    }
}
