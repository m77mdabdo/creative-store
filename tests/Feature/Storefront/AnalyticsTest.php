<?php

namespace Tests\Feature\Storefront;

use App\Support\Analytics\Analytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    public function test_no_tags_are_printed_when_nothing_is_configured(): void
    {
        config(['analytics.ga4_id' => null, 'analytics.meta_pixel_id' => null, 'analytics.snap_pixel_id' => null, 'analytics.tiktok_pixel_id' => null, 'analytics.debug' => false]);

        $this->get(route('products.index'))->assertOk()->assertDontSee('csTrack', false);
    }

    public function test_configured_tags_load_and_page_events_are_sent(): void
    {
        config(['analytics.ga4_id' => 'G-TEST123', 'analytics.meta_pixel_id' => '1234567890123456']);
        $product = $this->product(250, ['slug' => 'silk-scarf']);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('googletagmanager.com/gtag/js?id=G-TEST123', false)
            ->assertSee("fbq('init', \"1234567890123456\")", false)
            ->assertSee('csTrack("view_item"', false);

        $this->get(route('products.index', ['q' => 'abaya']))
            ->assertSee('csTrack("search"', false);
    }

    public function test_purchase_is_reported_once_per_order(): void
    {
        $order = $this->paidOrderFor($this->customer());

        Analytics::purchase($order);
        Analytics::purchase($order);

        $events = Analytics::pending();

        $this->assertCount(1, $events);
        $this->assertSame('purchase', $events[0]['event']);
        $this->assertSame($order->order_number, $events[0]['params']['transaction_id']);
        $this->assertSame([], Analytics::pending());
    }
}
