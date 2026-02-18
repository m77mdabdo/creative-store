<?php

namespace Tests\Feature\Policies;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnershipPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_can_act_only_on_their_own_orders(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        foreach (['view', 'cancel', 'pay', 'downloadInvoice'] as $ability) {
            $this->assertTrue($owner->can($ability, $order), "owner should be able to $ability");
            $this->assertFalse($other->can($ability, $order), "another customer must not $ability");
        }
    }

    public function test_guest_orders_belong_to_nobody(): void
    {
        $order = Order::factory()->create(['user_id' => null]);

        $this->assertFalse(User::factory()->create()->can('view', $order));
    }

    public function test_customers_can_change_only_their_own_addresses(): void
    {
        $owner = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $owner->id]);
        $other = User::factory()->create();

        $this->assertTrue($owner->can('update', $address));
        $this->assertFalse($other->can('update', $address));
        $this->assertFalse($other->can('delete', $address));

        $this->actingAs($other)->get(route('shipping.addresses.edit', $address))->assertForbidden();
        $this->actingAs($other)->delete(route('shipping.addresses.destroy', $address))->assertForbidden();
        $this->assertModelExists($address);
    }
}
