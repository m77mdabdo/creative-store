<?php

namespace Tests\Feature\Messaging;

use App\Models\Shipment;
use App\Notifications\OrderPlacedNotification;
use App\Services\Messaging\Messenger;
use App\Services\Shipping\ShipmentCarrierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class TextMessageTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'messaging.driver' => 'unifonic',
            'messaging.drivers.unifonic.app_sid' => 'test-app-sid',
            'mail.default' => 'array',
        ]);

        Http::fake(['el.cloud.unifonic.com/*' => Http::response(['success' => true])]);
    }

    #[DataProvider('numbers')]
    public function test_phone_numbers_are_normalized(?string $input, ?string $expected): void
    {
        $this->assertSame($expected, Messenger::normalize($input));
    }

    public static function numbers(): array
    {
        return [
            ['0512345678', '966512345678'],
            ['+966 51 234 5678', '966512345678'],
            ['00966512345678', '966512345678'],
            ['123', null],
            [null, null],
        ];
    }

    public function test_the_customer_gets_a_text_when_the_order_is_confirmed(): void
    {
        $customer = $this->customer(['phone' => '0512345678']);
        $order = $this->paidOrderFor($customer);

        $customer->notify(new OrderPlacedNotification($order));

        Http::assertSent(fn (Request $request) => $request['Recipient'] === '966512345678'
            && str_contains($request['Body'], $order->order_number)
            && $request['AppSid'] === 'test-app-sid');
    }

    public function test_staff_copies_of_the_order_notification_are_not_texted(): void
    {
        $order = $this->paidOrderFor($this->customer(['phone' => '0512345678']));
        $staff = $this->customer(['phone' => '0599999999']);

        $staff->notify(new OrderPlacedNotification($order));

        Http::assertNothingSent();
    }

    public function test_shipping_and_delivery_are_texted_with_the_tracking_number(): void
    {
        Storage::fake('local');
        $customer = $this->customer(['phone' => '0512345678']);
        $order = $this->paidOrderFor($customer);
        $shipment = Shipment::factory()->create(['order_id' => $order->id, 'tracking_number' => null, 'status' => 'pending', 'shipped_at' => null]);

        $service = app(ShipmentCarrierService::class);
        $shipment = $service->book($shipment);

        Http::assertSent(fn (Request $request) => str_contains($request['Body'], $shipment->tracking_number));

        $this->travel(31)->hours();
        $service->sync($shipment);

        Http::assertSentCount(2);
    }

    public function test_a_provider_failure_does_not_break_the_flow(): void
    {
        Http::fake(['*' => Http::response(['success' => false], 500)]);
        $customer = $this->customer(['phone' => '0512345678']);

        $customer->notify(new OrderPlacedNotification($this->paidOrderFor($customer)));

        $this->assertSame(1, $customer->notifications()->count());
    }
}
