<?php

namespace Tests\Feature\Storefront;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_checkout_button_leads_to_the_delivery_step(): void
    {
        $this->get(route('checkout.index'))->assertRedirect(route('shipping.index'));

        $this->actingAs(User::factory()->create())
            ->get(route('checkout.index'))
            ->assertRedirect(route('shipping.index'));
    }
}
