<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelledBroadcastEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public int $userId,
        public bool $wasPaidOnline = false
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('users.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'order.cancelled';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'order_cancelled',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'تم إلغاء الطلب بنجاح',
            'message' => $this->wasPaidOnline
                ? 'تم إلغاء طلبك بنجاح. سيتم إرجاع المبلغ إلى وسيلة الدفع خلال 14 يوم.'
                : 'تم إلغاء طلبك بنجاح.',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
