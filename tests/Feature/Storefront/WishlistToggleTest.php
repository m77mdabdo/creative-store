<?php

namespace Tests\Feature\Storefront;

use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class WishlistToggleTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    public function test_an_active_product_can_be_saved_and_removed(): void
    {
        $user = $this->customer();
        $product = $this->product();
        $variant = $this->variant($product);

        $this->actingAs($user)->post(route('wishlist.toggle'), ['product_id' => $product->id, 'variant_id' => $variant->id])
            ->assertSessionHasNoErrors();
        $this->assertSame(1, WishlistItem::count());

        $this->actingAs($user)->post(route('wishlist.toggle'), ['product_id' => $product->id, 'variant_id' => $variant->id]);
        $this->assertSame(0, WishlistItem::count());
    }

    public function test_hidden_products_and_other_products_variants_are_refused(): void
    {
        $user = $this->customer();
        $hidden = $this->product(100, ['status' => 'draft']);
        $product = $this->product();
        $otherVariant = $this->variant($this->product());

        $this->actingAs($user)->post(route('wishlist.toggle'), ['product_id' => $hidden->id])
            ->assertSessionHasErrors('product_id');
        $this->actingAs($user)->post(route('wishlist.toggle'), ['product_id' => $product->id, 'variant_id' => $otherVariant->id])
            ->assertSessionHasErrors('variant_id');

        $this->assertSame(0, WishlistItem::count());
    }
}
