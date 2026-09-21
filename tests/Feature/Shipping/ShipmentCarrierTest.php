<?php

namespace Tests\Feature\Shipping;

use App\Models\Shipment;
use App\Services\Shipping\ShipmentCarrierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class ShipmentCarrierTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    private function pendingShipment(): Shipment
    {
        $order = $this->paidOrderFor($this->customer());
        $order->addressSnapshot()->create(['full_name' => 'Sara Ali', 'phone' => '0500000000', 'country' => 'Saudi Arabia', 'city' => 'Jeddah', 'street' => 'Tahlia']);

        return Shipment::factory()->create([
            'order_id' => $order->id, 'carrier' => null, 'tracking_number' => null,
            'status' => Shipment::STATUS_PENDING, 'shipped_at' => null, 'delivered_at' => null,
        ]);
    }

    public function test_booking_creates_a_waybill_label_and_ships_the_order(): void
    {
        Storage::fake('local');
        $shipment = $this->pendingShipment();
        $service = app(ShipmentCarrierService::class);

        $shipment = $service->book($shipment);

        $this->assertStringStartsWith('SBX', $shipment->tracking_number);
        $this->assertSame(Shipment::STATUS_SHIPPED, $shipment->status);
        Storage::disk('local')->assertExists($service->labelPath($shipment->tracking_number));
        $this->assertSame('shipped', $shipment->order->fresh()->status);

        $this->expectException(RuntimeException::class);
        $service->book($shipment);
    }

    public function test_tracking_advances_to_delivered_and_completes_the_order(): void
    {
        Storage::fake('local');
        $service = app(ShipmentCarrierService::class);
        $shipment = $service->book($this->pendingShipment());

        $this->travel(31)->hours();

        $this->assertSame(4, $service->sync($shipment->fresh()));
        $this->assertSame(0, $service->sync($shipment->fresh()));

        $shipment->refresh();
        $this->assertSame(Shipment::STATUS_DELIVERED, $shipment->status);
        $this->assertSame('Jeddah', $shipment->current_location);
        $this->assertNotNull($shipment->delivered_at);
        $this->assertSame('delivered', $shipment->order->fresh()->status);
    }

    public function test_the_sync_command_runs(): void
    {
        $this->artisan('shipments:sync')->assertSuccessful();
    }
}
