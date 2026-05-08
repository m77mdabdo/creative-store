<?php

namespace Tests\Feature\Storefront;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class ReviewRulesTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    private function review(int $productId)
    {
        return $this->actingAs($this->customer())->post(route('reviews.store'), [
            'product_id' => $productId,
            'rating'     => 5,
            'comment'    => 'Great product',
        ]);
    }

    public function test_draft_and_archived_products_take_no_reviews(): void
    {
        $this->review($this->product(100, ['status' => 'draft'])->id)->assertNotFound();
        $this->review($this->product(100, ['status' => 'archived'])->id)->assertNotFound();

        $this->assertSame(0, Review::count());
    }

    public function test_products_on_sale_can_be_reviewed(): void
    {
        $this->review($this->product()->id)->assertRedirect();

        $this->assertSame(1, Review::count());
    }
}
