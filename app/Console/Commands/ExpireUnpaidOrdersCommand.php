<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\Coupons\CouponRedemptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Cancels orders that were never paid and returns their reserved stock.
 */
class ExpireUnpaidOrdersCommand extends Command
{
    protected $signature = 'orders:expire-unpaid {--minutes=60 : Age after which an unpaid order expires}';

    protected $description = 'Cancel unpaid online orders and release their stock';

    private CouponRedemptionService $coupons;

    public function handle(CouponRedemptionService $coupons): int
    {
        $this->coupons = $coupons;

        $cutoff = now()->subMinutes(max(15, (int) $this->option('minutes')));
        $expired = 0;

        Order::query()
            ->where('status', 'pending')
            ->whereIn('payment_status', ['pending', 'unpaid', 'failed'])
            ->where('placed_at', '<', $cutoff)
            ->select('id')
            ->chunkById(100, function ($orders) use (&$expired) {
                foreach ($orders as $order) {
                    $expired += (int) $this->expire($order->id);
                }
            });

        $this->info("Expired {$expired} unpaid order(s).");

        return self::SUCCESS;
    }

    private function expire(int $orderId): bool
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::query()->with('items.variant')->lockForUpdate()->find($orderId);

            if (! $order || $order->status !== 'pending' || $order->payment_status === 'paid') {
                return false;
            }

            foreach ($order->items as $item) {
                $item->variant?->increment('stock', (int) $item->quantity);
            }

            $this->coupons->release($order);

            $order->update([
                'status'              => 'cancelled',
                'payment_status'      => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => 'Payment not completed in time.',
            ]);

            OrderStatusLog::create([
                'order_id'    => $order->id,
                'from_status' => 'pending',
                'to_status'   => 'cancelled',
                'changed_by'  => null,
                'note'        => 'Expired automatically: payment not completed.',
            ]);

            return true;
        });
    }
}
