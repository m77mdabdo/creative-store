<?php

namespace Tests\Feature\Shipping;

use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Support\Shipping\DeliveryTiers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryTiersTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiers_set_delivery_days_and_add_next_day_for_big_cities(): void
    {
        $zone = ShippingZone::factory()->create(['country' => 'Saudi Arabia', 'is_active' => true]);
        $standard = ShippingMethod::factory()->create(['code' => 'standard', 'is_active' => true]);
        $express = ShippingMethod::factory()->create(['code' => 'express', 'is_active' => true]);

        $rate = fn ($method, $city, $price) => ShippingRate::factory()->create([
            'shipping_zone_id' => $zone->id, 'shipping_method_id' => $method->id, 'city' => $city,
            'district' => null, 'price' => $price, 'eta_min_days' => 1, 'eta_max_days' => 2,
        ]);

        $rate($standard, 'Riyadh', 20);
        $rate($express, 'Riyadh', 30);
        $rate($express, null, 45);

        DeliveryTiers::apply();
        DeliveryTiers::apply(); // safe to run again

        $nextDay = ShippingMethod::where('code', 'next-day')->sole();
        $this->assertSame(3, $nextDay->sort_order);

        $this->assertSame([5, 7], [ShippingRate::where('shipping_method_id', $standard->id)->value('eta_min_days'), ShippingRate::where('shipping_method_id', $standard->id)->value('eta_max_days')]);
        $this->assertSame(2, ShippingRate::where('shipping_method_id', $express->id)->where('eta_min_days', 3)->where('eta_max_days', 4)->count());

        $nextDayRates = ShippingRate::where('shipping_method_id', $nextDay->id)->get();
        $this->assertCount(1, $nextDayRates);
        $this->assertSame('Riyadh', $nextDayRates[0]->city);
        $this->assertEquals(40, $nextDayRates[0]->price);
        $this->assertSame(1, $nextDayRates[0]->eta_max_days);
    }
}
