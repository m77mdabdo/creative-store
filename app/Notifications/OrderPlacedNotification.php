<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedNotification extends Notification
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
    }

    public function via($notifiable)
    {
        return ['mail','database'];
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
