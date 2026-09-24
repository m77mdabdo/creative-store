<?php

namespace App\Support\Analytics;

use App\Models\Order;

/**
 * E-commerce events for GA4, Meta, Snap and TikTok pixels.
 *
 * Events raised during a request that ends in a redirect (add to cart) are
 * flashed to the session and fired on the next page, so none are lost.
 * Parameters follow GA4's names; the browser side maps them to each pixel.
 */
final class Analytics
{
    private const SESSION_KEY = 'analytics.events';

    private static array $events = [];

    public static function enabled(): bool
    {
        return (bool) (config('analytics.ga4_id') || config('analytics.meta_pixel_id')
            || config('analytics.snap_pixel_id') || config('analytics.tiktok_pixel_id')
            || config('analytics.debug'));
    }

    /**
     * Fire an event on the current page.
     */
    public static function track(string $event, array $params = []): void
    {
        self::$events[] = ['event' => $event, 'params' => $params];
    }

    /**
     * Fire an event on the next page (use before a redirect).
     */
    public static function flash(string $event, array $params = []): void
    {
        $queued = session()->get(self::SESSION_KEY, []);
        $queued[] = ['event' => $event, 'params' => $params];
        session()->flash(self::SESSION_KEY, $queued);
    }

    /**
     * Events to output on this page (flashed ones first).
     */
    public static function pending(): array
    {
        $events = array_merge(session()->pull(self::SESSION_KEY, []), self::$events);
        self::$events = [];

        return $events;
    }

    public static function item(int|string $id, string $name, float $price, int $quantity = 1, ?string $category = null, ?string $variant = null): array
    {
        return array_filter([
            'item_id'       => (string) $id,
            'item_name'     => $name,
            'price'         => round($price, 2),
            'quantity'      => $quantity,
            'item_category' => $category,
            'item_variant'  => $variant,
        ], fn ($value) => $value !== null);
    }

    /**
     * The purchase event for an order, fired once per order per session.
     */
    public static function purchase(Order $order): void
    {
        $key = 'analytics.purchased.' . $order->id;

        if (session()->has($key)) {
            return;
        }

        session()->put($key, true);
        $order->loadMissing('items');

        self::track('purchase', [
            'transaction_id' => $order->order_number,
            'value'          => round((float) $order->total, 2),
            'tax'            => round((float) $order->tax_total, 2),
            'shipping'       => round((float) $order->shipping_fee, 2),
            'currency'       => $order->currency ?: 'SAR',
            'items'          => $order->items->map(fn ($item) => self::item(
                $item->product_id,
                (string) $item->product_name_snapshot,
                (float) $item->unit_price,
                (int) $item->quantity,
                null,
                trim(implode(' / ', array_filter([$item->color, $item->size]))) ?: null,
            ))->values()->all(),
        ]);
    }
}
