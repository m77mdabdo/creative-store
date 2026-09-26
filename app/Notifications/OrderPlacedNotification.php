<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\TextMessageChannel;
use App\Services\Messaging\TextMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load([
            'items',
            'addressSnapshot',
            'shippingMethod'
        ]);

        // Queued, and only once the payment transaction has committed.
        $this->afterCommit();
    }

    public function via($notifiable)
    {
        // The customer also gets a text message; staff copies do not.
        return (int) $notifiable->getKey() === (int) $this->order->user_id
            ? ['mail', 'database', TextMessageChannel::class]
            : ['mail', 'database'];
    }

    public function toText($notifiable): TextMessage
    {
        return new TextMessage(
            __('notifications.text.order_placed', [
                'number' => $this->order->order_number,
                'total'  => money($this->order->total, $this->order->currency),
            ], config('app.locale')),
            $this->order->addressSnapshot?->phone ?? $notifiable->phone,
        );
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Confirmation #' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your order has been placed successfully.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total: ' . number_format($this->order->total,2) . ' ' . $this->order->currency)
            ->line('Payment Status: ' . ucfirst($this->order->payment_status))
            ->action('View Order', url('/shipping/orders/'.$this->order->id))
            ->line('Thank you for shopping with Creative Store.');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
        ];
    }
}
