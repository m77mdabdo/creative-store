<?php

namespace Tests\Feature\Checkout;

use App\Exceptions\CheckoutException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Checkout\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class SimpleProductStockTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    private function simpleProduct(?int $stock): Product
    {
        return $this->product(100, ['has_variants' => false, 'stock' => $stock]);
    }

    private function line(Product $product, int $quantity): array
    {
        return [
            'product_id' => $product->id,
            'variant_id' => null,
            'quantity'   => $quantity,
            'name'       => 'Simple product',
            'price'      => 100.0,
        ];
    }

    private function place(Product $product, int $quantity): Order
    {
        $user = $this->customer();
        $rate = $this->shippingRate(20);

        return app(CheckoutService::class)->placeOrder(
            $user, $this->address($user), $rate, $rate->shipping_method_id,
            [$this->line($product, $quantity)], $this->totals(100 * $quantity), null,
        );
    }

    public function test_checkout_takes_units_from_a_stock_tracked_product(): void
    {
        $product = $this->simpleProduct(5);

        $this->place($product, 2);

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_checkout_refuses_more_units_than_the_product_has(): void
    {
        $product = $this->simpleProduct(1);

        try {
            $this->place($product, 2);
            $this->fail('The order should have been refused.');
        } catch (CheckoutException) {
            $this->assertSame(1, $product->fresh()->stock);
            $this->assertSame(0, Order::count());
        }
    }

    public function test_products_without_a_stock_value_sell_as_before(): void
    {
        $product = $this->simpleProduct(null);

        $this->place($product, 3);

        $this->assertNull($product->fresh()->stock);
        $this->assertSame(1, Order::count());
    }

    public function test_the_cart_refuses_more_than_the_stock(): void
    {
        $product = $this->simpleProduct(2);

        $this->actingAs($this->customer())
            ->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 3])
            ->assertSessionHas('error');

        $this->assertSame([], session('cart', []));

        $this->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 2])
            ->assertSessionHas('success');
    }

    public function test_a_sold_out_product_cannot_be_added(): void
    {
        $product = $this->simpleProduct(0);

        $this->actingAs($this->customer())
            ->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 1])
            ->assertSessionHas('error', __('This product is out of stock.'));
    }

    public function test_an_expired_order_puts_the_units_back(): void
    {
        $product = $this->simpleProduct(5);
        $order = $this->place($product, 2);
        $order->forceFill(['created_at' => now()->subHours(3), 'placed_at' => now()->subHours(3)])->save();

        $this->artisan('orders:expire-unpaid', ['--minutes' => 60])->assertSuccessful();

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_restock_leaves_untracked_products_untracked(): void
    {
        $product = $this->simpleProduct(null);
        $item = OrderItem::factory()->create(['product_id' => $product->id, 'variant_id' => null, 'quantity' => 2]);

        $item->restock();

        $this->assertNull($product->fresh()->stock);
    }
}
