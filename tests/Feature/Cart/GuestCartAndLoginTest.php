<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class GuestCartAndLoginTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    private Product $product;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = $this->product(120);
        $this->variant = $this->variant($this->product, stock: 10);
    }

    private function addToCart(int $quantity = 2)
    {
        return $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'quantity'   => $quantity,
        ]);
    }

    public function test_a_guest_can_fill_change_and_view_the_cart(): void
    {
        $this->addToCart(2)->assertRedirect();

        $key = $this->product->id . '_' . $this->variant->id;
        $this->assertSame(2, session('cart')[$key]['quantity']);

        $this->post(route('cart.update'), ['key' => $key, 'quantity' => 3])->assertRedirect();
        $this->assertSame(3, session('cart')[$key]['quantity']);
        $this->get(route('cart.index'))->assertOk()->assertSee(__('site.checkout_login_hint'));

        $this->delete(route('cart.remove', $key))->assertRedirect();
        $this->assertEmpty(session('cart', []));

        // Nothing is saved for guests.
        $this->assertSame(0, Cart::count());
    }

    public function test_checkout_asks_a_guest_to_sign_in_and_returns_with_the_cart(): void
    {
        $user = $this->customer(['password' => bcrypt('Secret#2026')]);
        $this->addToCart(2);

        $this->get(route('shipping.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('info', __('site.login_to_checkout'));

        $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'Secret#2026'])
            ->assertRedirect(route('shipping.index'));

        $this->assertAuthenticatedAs($user);
        $this->assertCount(1, session('cart'));

        // The cart is now saved for the customer.
        $saved = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($saved);
        $this->assertSame(2, $saved->items()->first()->quantity);

        $this->get(route('shipping.index'))->assertOk();
    }

    public function test_a_saved_cart_follows_the_customer_to_a_new_session(): void
    {
        $user = $this->customer();
        $other = $this->product(80);
        $otherVariant = $this->variant($other, stock: 5);

        // Device 1: signed in, adds an item.
        $this->actingAs($user)->post(route('cart.add'), ['product_id' => $other->id, 'variant_id' => $otherVariant->id, 'quantity' => 1]);
        $this->assertSame(1, Cart::where('user_id', $user->id)->first()->items()->count());

        // Device 2: a fresh session with a guest cart, then the same customer signs in.
        $this->flushSession();
        auth()->logout();
        $this->addToCart(2);
        $this->actingAs($user)->get(route('cart.index'))->assertOk();

        $keys = array_keys(session('cart'));
        sort($keys);
        $expected = [$this->product->id . '_' . $this->variant->id, $other->id . '_' . $otherVariant->id];
        sort($expected);
        $this->assertSame($expected, $keys);
        $this->assertSame(2, Cart::where('user_id', $user->id)->first()->items()->count());
    }

    public function test_emptying_the_cart_removes_the_saved_copy(): void
    {
        $user = $this->customer();

        $this->actingAs($user)->post(route('cart.add'), ['product_id' => $this->product->id, 'variant_id' => $this->variant->id, 'quantity' => 1]);
        $this->assertSame(1, Cart::where('user_id', $user->id)->count());

        $this->actingAs($user)->post(route('cart.clear'));

        $this->assertSame(0, Cart::where('user_id', $user->id)->count());
    }

    public function test_wishlist_and_order_pages_still_require_an_account(): void
    {
        $this->get(route('wishlist.index'))->assertRedirect(route('login'));
        $this->get(route('shipping.orders.index'))->assertRedirect(route('login'));
    }
}
