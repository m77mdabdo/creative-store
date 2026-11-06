<?php

namespace Tests\Feature\Tracking;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Support\Tracking\OrderTracking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    private User $owner;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = $this->customer(['phone' => '+966512345678']);
        $method = ShippingMethod::factory()->create(['code' => 'express']);

        $this->order = Order::factory()->create([
            'order_number'            => 'ORD-261115-ABC123',
            'user_id'                 => $this->owner->id,
            'shipping_method_id'      => $method->id,
            'status'                  => 'confirmed',
            'payment_status'          => 'paid',
            'paid_at'                 => now()->subHour(),
            'placed_at'               => now()->subHours(2),
            'estimated_delivery_from' => now()->addDays(3),
            'estimated_delivery_to'   => now()->addDays(4),
        ]);
        $this->order->addressSnapshot()->create([
            'full_name' => 'Sara Ali', 'phone' => '0555555555', 'country' => 'Saudi Arabia', 'city' => 'Riyadh', 'street' => 'Olaya',
        ]);
        OrderItem::factory()->create(['order_id' => $this->order->id, 'product_name_snapshot' => 'Linen Shirt', 'quantity' => 2]);
    }

    public function test_a_guest_finds_the_order_with_its_number_and_phone_in_any_format(): void
    {
        $this->post(route('tracking.track'), ['order_number' => 'ord-261115-abc123', 'phone' => '0512345678'])
            ->assertRedirect(route('tracking.show', 'ORD-261115-ABC123'));

        $this->get(route('tracking.show', 'ORD-261115-ABC123'))
            ->assertOk()
            ->assertSee('Your order is confirmed')
            ->assertSee('Linen Shirt')
            ->assertSee('Being prepared');

        // The phone on the delivery address works too.
        $this->post(route('tracking.track'), ['order_number' => 'ORD-261115-ABC123', 'phone' => '+966 55 555 5555'])
            ->assertRedirect(route('tracking.show', 'ORD-261115-ABC123'));
    }

    public function test_wrong_details_reveal_nothing(): void
    {
        $this->from(route('tracking.search-form'))
            ->post(route('tracking.track'), ['order_number' => 'ORD-261115-ABC123', 'phone' => '0599999999'])
            ->assertRedirect(route('tracking.search-form'))
            ->assertSessionHasErrors('tracking');

        $this->from(route('tracking.search-form'))
            ->post(route('tracking.track'), ['order_number' => 'ORD-261115-ABC123'])
            ->assertSessionHasErrors('tracking');

        $this->get(route('tracking.show', 'ORD-261115-ABC123'))->assertRedirect(route('tracking.search-form'));
        $this->get(route('tracking.status', 'ORD-261115-ABC123'))->assertNotFound();

        $this->actingAs($this->customer())
            ->get(route('tracking.show', 'ORD-261115-ABC123'))
            ->assertRedirect(route('tracking.search-form'));
    }

    public function test_the_owner_opens_tracking_from_their_account(): void
    {
        $this->actingAs($this->owner)
            ->get(route('tracking.search-form'))
            ->assertOk()
            ->assertSee('ORD-261115-ABC123');

        $this->actingAs($this->owner)
            ->get(route('tracking.show', 'ORD-261115-ABC123'))
            ->assertOk();

        $this->actingAs($this->owner)
            ->getJson(route('tracking.status', 'ORD-261115-ABC123'))
            ->assertOk()
            ->assertJson(['stage' => 'confirmed', 'status' => 'confirmed']);
    }

    public function test_tracking_number_lookup_and_courier_progress(): void
    {
        $shipment = Shipment::factory()->create([
            'order_id' => $this->order->id, 'tracking_number' => 'SBX0000011234', 'carrier' => 'sandbox',
            'status' => Shipment::STATUS_IN_TRANSIT, 'shipped_at' => now()->subHours(20), 'delivered_at' => null,
            'current_location' => 'Riyadh',
        ]);
        ShipmentTrackingLog::create(['shipment_id' => $shipment->id, 'status' => 'shipped', 'carrier_status' => 'Shipment picked up', 'location' => 'Riyadh hub', 'tracked_at' => now()->subHours(20)]);
        ShipmentTrackingLog::create(['shipment_id' => $shipment->id, 'status' => 'in_transit', 'carrier_status' => 'Out for delivery', 'location' => 'Riyadh', 'tracked_at' => now()->subHour()]);

        $this->post(route('tracking.track'), ['tracking_number' => 'sbx0000011234'])
            ->assertRedirect(route('tracking.show', 'ORD-261115-ABC123'));

        $this->get(route('tracking.show', 'ORD-261115-ABC123'))
            ->assertOk()
            ->assertSee('Arriving today, keep your phone close!')
            ->assertSee('Picked up by the courier')
            ->assertSee('SBX0000011234');

        $tracking = OrderTracking::for($this->order->fresh());
        $this->assertSame('out_for_delivery', $tracking->currentStage());
        $this->assertSame(80, $tracking->progress());
    }

    public function test_delivered_and_cancelled_orders(): void
    {
        $this->order->update(['status' => 'delivered', 'shipping_status' => 'delivered']);
        $tracking = OrderTracking::for($this->order->fresh());
        $this->assertTrue($tracking->isDelivered());
        $this->assertSame(100, $tracking->progress());

        $this->order->update(['status' => 'cancelled']);
        $tracking = OrderTracking::for($this->order->fresh());
        $this->assertSame('confirmed', $tracking->currentStage()); // it was paid before cancelling

        $this->actingAs($this->owner)
            ->get(route('tracking.show', 'ORD-261115-ABC123'))
            ->assertOk()
            ->assertSee('This order was cancelled');
    }
}
