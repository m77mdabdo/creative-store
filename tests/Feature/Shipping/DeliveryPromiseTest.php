<?php

namespace Tests\Feature\Shipping;

use App\Support\Shipping\DeliveryPromise;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class DeliveryPromiseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'store.delivery.timezone'    => 'Asia/Riyadh',
            'store.delivery.days_off'    => [5],
            'store.delivery.cutoff_hour' => 14,
        ]);
        app()->setLocale('en');
    }

    private function at(string $time): void
    {
        $this->travelTo(CarbonImmutable::parse($time, 'Asia/Riyadh'));
    }

    public function test_same_day_and_next_day_before_the_cut_off(): void
    {
        $this->at('2026-11-15 10:00'); // Sunday morning

        $today = DeliveryPromise::make(0, 0);
        $this->assertSame('Today', $today->label());
        $this->assertSame('Arrives today', $today->headline());
        $this->assertSame('Order within 4 h 0 min', $today->cutoffHint());

        $this->assertSame('Tomorrow', DeliveryPromise::make(1, 1)->label());
    }

    public function test_standard_delivery_counts_working_days_and_skips_friday(): void
    {
        $this->at('2026-11-15 10:00');

        $promise = DeliveryPromise::make(5, 7);

        $this->assertSame('2026-11-21', $promise->from->toDateString()); // Sat (Friday skipped)
        $this->assertSame('2026-11-23', $promise->to->toDateString());
        $this->assertSame('21 – 23 Nov', $promise->label());
        $this->assertSame('5 – 7 days', $promise->window());
        $this->assertNull($promise->cutoffHint());

        $this->assertSame('2026-11-18', DeliveryPromise::make(3, 4)->from->toDateString());
    }

    public function test_orders_after_the_cut_off_move_a_day(): void
    {
        $this->at('2026-11-15 15:30');

        $this->assertSame('Tomorrow', DeliveryPromise::make(0, 0)->label());
        $this->assertNull(DeliveryPromise::make(0, 0)->cutoffHint());

        // Thursday evening: Friday is off, so next-day lands on Sunday.
        $this->at('2026-11-19 16:00');
        $this->assertSame('2026-11-22', DeliveryPromise::make(1, 1)->from->toDateString());
        $this->assertSame('Sun, 22 Nov', DeliveryPromise::make(1, 1)->label());
    }

    public function test_labels_follow_the_language(): void
    {
        $this->at('2026-11-15 10:00');
        app()->setLocale('ar');

        $this->assertSame('اليوم', DeliveryPromise::make(0, 0)->label());
        $this->assertSame('بكرة', DeliveryPromise::make(1, 1)->label());
        $this->assertSame('يوصلك بكرة', DeliveryPromise::make(1, 1)->headline());
    }

    public function test_promise_from_stored_order_dates(): void
    {
        $this->at('2026-11-15 10:00');

        $this->assertNull(DeliveryPromise::forOrder(null, null));

        $promise = DeliveryPromise::forOrder(now()->subDays(3), now()->subDay());
        $this->assertTrue($promise->isLate());
    }
}
