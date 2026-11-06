<?php

namespace App\Support\Tracking;

use App\Models\Order;
use App\Models\Shipment;
use App\Support\Shipping\DeliveryPromise;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * Everything the tracking page shows for one order: the journey from
 * "placed" to "delivered", how far along it is, the delivery promise and
 * a feed of every update from the store and the courier.
 *
 * Stages are derived from the order, its status history and the carrier's
 * tracking log, so they stay right whichever of those moved last.
 */
final class OrderTracking
{
    public const STAGES = ['placed', 'confirmed', 'preparing', 'shipped', 'out_for_delivery', 'delivered'];

    private const ICONS = [
        'placed'           => 'fa-receipt',
        'confirmed'        => 'fa-check',
        'preparing'        => 'fa-box-open',
        'shipped'          => 'fa-truck',
        'out_for_delivery' => 'fa-shipping-fast',
        'delivered'        => 'fa-gift',
    ];

    private const PICKUP_ICONS = [
        'shipped'          => 'fa-store',
        'out_for_delivery' => 'fa-hand-holding',
        'delivered'        => 'fa-gift',
    ];

    public readonly ?Shipment $shipment;

    public readonly bool $isPickup;

    private function __construct(public readonly Order $order)
    {
        $this->shipment = $order->shipment;
        $this->isPickup = $order->shippingMethod?->code === 'pickup';
    }

    public static function for(Order $order): self
    {
        $order->loadMissing([
            'shippingMethod:id,code,name',
            'shipment.trackingLogs',
            'shipment.deliveryAttempts',
            'statusLogs',
            'addressSnapshot',
            'items',
        ]);

        return new self($order);
    }

    public function isCancelled(): bool
    {
        return $this->order->status === 'cancelled';
    }

    public function isDelivered(): bool
    {
        return $this->reached('delivered');
    }

    /** "placed" ... "delivered": the furthest stage the order has reached. */
    public function currentStage(): string
    {
        $current = 'placed';

        foreach (self::STAGES as $stage) {
            if ($this->reached($stage)) {
                $current = $stage;
            }
        }

        return $current;
    }

    public function currentIndex(): int
    {
        return (int) array_search($this->currentStage(), self::STAGES, true);
    }

    /** 0 – 100, for the progress bar. */
    public function progress(): int
    {
        return (int) round($this->currentIndex() / (count(self::STAGES) - 1) * 100);
    }

    /**
     * @return list<object{key: string, icon: string, label: string, at: ?CarbonInterface, done: bool, current: bool}>
     */
    public function steps(): array
    {
        $currentIndex = $this->currentIndex();

        return array_map(function (string $stage, int $index) use ($currentIndex) {
            $key = $this->isPickup && isset(self::PICKUP_ICONS[$stage]) ? 'pickup_' . $stage : $stage;

            return (object) [
                'key'     => $stage,
                'icon'    => $this->isPickup ? (self::PICKUP_ICONS[$stage] ?? self::ICONS[$stage]) : self::ICONS[$stage],
                'label'   => __('tracking.stages.' . $key),
                'at'      => $index <= $currentIndex ? $this->reachedAt($stage) : null,
                'done'    => $index <= $currentIndex,
                'current' => $index === $currentIndex && ! $this->isDelivered(),
            ];
        }, self::STAGES, array_keys(self::STAGES));
    }

    /** The friendly sentence at the top of the page. */
    public function headline(): string
    {
        if ($this->isCancelled()) {
            return __('tracking.headline.cancelled');
        }

        if ($this->shipment?->status === Shipment::STATUS_FAILED) {
            return __('tracking.headline.failed');
        }

        $stage = $this->currentStage();

        return __('tracking.headline.' . ($this->isPickup && in_array($stage, ['shipped', 'out_for_delivery', 'delivered'], true) ? 'pickup_' . $stage : $stage));
    }

    public function subline(): string
    {
        if ($this->isCancelled()) {
            return __('tracking.subline.cancelled');
        }

        if ($this->order->status === 'pending' && $this->order->payment_status !== 'paid' && $this->order->payment_method !== 'cod') {
            return __('tracking.subline.awaiting_payment');
        }

        return __('tracking.subline.' . $this->currentStage());
    }

    public function promise(): ?DeliveryPromise
    {
        return DeliveryPromise::forOrder($this->order->estimated_delivery_from, $this->order->estimated_delivery_to);
    }

    public function deliveredAt(): ?CarbonInterface
    {
        return $this->reachedAt('delivered');
    }

    /**
     * Every update, newest first: order status changes and courier scans.
     *
     * @return Collection<int, object{at: CarbonInterface, title: string, location: ?string, tone: string}>
     */
    public function events(): Collection
    {
        $events = collect([
            (object) [
                'at'       => $this->order->placed_at ?? $this->order->created_at,
                'title'    => __('tracking.events.placed'),
                'location' => null,
                'tone'     => 'primary',
            ],
        ]);

        if ($this->order->paid_at) {
            $events->push((object) [
                'at' => $this->order->paid_at, 'title' => __('tracking.events.paid'), 'location' => null, 'tone' => 'success',
            ]);
        }

        foreach ($this->order->statusLogs as $log) {
            if (! $log->to_status || $log->to_status === $log->from_status || $log->to_status === 'pending') {
                continue;
            }

            $events->push((object) [
                'at'       => $log->created_at,
                'title'    => Lang::has('tracking.events.status_' . $log->to_status)
                    ? __('tracking.events.status_' . $log->to_status)
                    : status_label($log->to_status),
                'location' => null,
                'tone'     => $log->to_status === 'cancelled' ? 'danger' : 'primary',
            ]);
        }

        foreach ($this->shipment?->trackingLogs ?? [] as $log) {
            $events->push((object) [
                'at'       => $log->tracked_at ?? $log->created_at,
                'title'    => self::translateCarrierText($log->carrier_status ?: $log->status),
                'location' => self::translateCarrierText($log->location),
                'tone'     => $log->status === Shipment::STATUS_DELIVERED ? 'success' : 'info',
            ]);
        }

        foreach ($this->shipment?->deliveryAttempts ?? [] as $attempt) {
            if ($attempt->status === 'failed' || $attempt->failure_reason) {
                $events->push((object) [
                    'at'       => $attempt->attempted_at ?? $attempt->created_at,
                    'title'    => __('tracking.events.attempt_failed'),
                    'location' => $attempt->failure_reason,
                    'tone'     => 'warning',
                ]);
            }
        }

        return $events
            ->filter(fn ($event) => $event->at !== null)
            ->sortByDesc(fn ($event) => $event->at->getTimestamp())
            ->unique(fn ($event) => $event->title . '|' . $event->at->getTimestamp())
            ->values();
    }

    /** Courier messages arrive in English; show the customer's language when we know the phrase. */
    public static function translateCarrierText(?string $text): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $key = 'tracking.courier_text.' . Str::slug($text, '_');

        return Lang::has($key) ? __($key) : $text;
    }

    private function reached(string $stage): bool
    {
        if ($this->isCancelled()) {
            return $stage === 'placed' || ($stage === 'confirmed' && $this->order->paid_at !== null);
        }

        $shipmentStatus = $this->shipment?->status;
        $orderStatus = $this->order->status;

        return match ($stage) {
            'placed'           => true,
            'confirmed'        => $this->order->payment_status === 'paid'
                || in_array($orderStatus, ['confirmed', 'processing', 'shipped', 'delivered', 'completed'], true)
                || $this->shipment !== null,
            'preparing'        => in_array($orderStatus, ['processing', 'shipped', 'delivered', 'completed'], true)
                || in_array($shipmentStatus, [Shipment::STATUS_PROCESSING, Shipment::STATUS_SHIPPED, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_DELIVERED], true),
            'shipped'          => $this->shipment?->shipped_at !== null
                || in_array($shipmentStatus, [Shipment::STATUS_SHIPPED, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_DELIVERED], true)
                || in_array($this->order->shipping_status, ['shipped', 'in_transit', 'delivered'], true)
                || in_array($orderStatus, ['shipped', 'delivered', 'completed'], true),
            'out_for_delivery' => $this->outForDeliveryAt() !== null || $this->reached('delivered'),
            'delivered'        => $shipmentStatus === Shipment::STATUS_DELIVERED
                || $this->shipment?->delivered_at !== null
                || $this->order->shipping_status === 'delivered'
                || in_array($orderStatus, ['delivered', 'completed'], true),
        };
    }

    private function reachedAt(string $stage): ?CarbonInterface
    {
        return match ($stage) {
            'placed'           => $this->order->placed_at ?? $this->order->created_at,
            'confirmed'        => $this->order->paid_at ?? $this->statusChangedAt('confirmed') ?? $this->statusChangedAt('processing'),
            'preparing'        => $this->statusChangedAt('processing'),
            'shipped'          => $this->shipment?->shipped_at ?? $this->statusChangedAt('shipped'),
            'out_for_delivery' => $this->outForDeliveryAt(),
            'delivered'        => $this->shipment?->delivered_at ?? $this->statusChangedAt('delivered') ?? $this->statusChangedAt('completed'),
        };
    }

    private function statusChangedAt(string $status): ?CarbonInterface
    {
        return $this->order->statusLogs
            ->where('to_status', $status)
            ->sortBy('created_at')
            ->first()?->created_at;
    }

    private function outForDeliveryAt(): ?CarbonInterface
    {
        $log = $this->shipment?->trackingLogs
            ->first(fn ($log) => Str::contains(Str::lower((string) $log->carrier_status), 'out for delivery'));

        return $log ? ($log->tracked_at ?? $log->created_at) : null;
    }
}
