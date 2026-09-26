<?php

namespace App\Notifications;

use App\Models\Shipment;
use App\Notifications\Channels\TextMessageChannel;
use App\Services\Messaging\TextMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Tells the customer their order shipped (with the tracking number) or
 * was delivered.
 */
class ShipmentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Shipment $shipment, public readonly string $event)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['database', TextMessageChannel::class];
    }

    public function toText(object $notifiable): TextMessage
    {
        $order = $this->shipment->order;

        return new TextMessage(
            __("notifications.text.{$this->event}", [
                'number'   => $order?->order_number,
                'tracking' => $this->shipment->tracking_number,
                'carrier'  => $this->shipment->carrier,
            ], config('app.locale')),
            $order?->addressSnapshot?->phone ?? $notifiable->phone ?? null,
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'shipment_' . $this->event,
            'order_id'        => $this->shipment->order_id,
            'order_number'    => $this->shipment->order?->order_number,
            'tracking_number' => $this->shipment->tracking_number,
        ];
    }
}
