<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderCancelledNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public int $orderId,
        public bool $wasPaidOnline = false
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $order = Order::query()
            ->with('user')
            ->find($this->orderId);

        if (! $order || ! $order->user) {
            return;
        }

        $order->user->notify(
            new OrderCancelledNotification($order, $this->wasPaidOnline)
        );
    }
}
