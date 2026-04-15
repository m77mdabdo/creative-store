<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Services\Payments\Moyasar\MoyasarPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderPaymentService
{
    public const RESULT_PAID = 'paid';

    public const RESULT_ALREADY_PAID = 'already_paid';

    public const RESULT_FAILED = 'failed';

    public const RESULT_PENDING = 'pending';

    public const RESULT_MISMATCH = 'mismatch';

    /**
     * Apply a verified gateway payment to its order.
     *
     * Safe to call more than once for the same payment (callback and webhook
     * may both arrive): the order row is locked and a paid order is never
     * confirmed or notified twice.
     */
    public function apply(Order $order, MoyasarPayment $remote, string $source): string
    {
        $result = DB::transaction(function () use ($order, $remote, $source) {
            /** @var Order $order */
            $order = Order::query()->lockForUpdate()->findOrFail($order->getKey());

            if ($order->payment_status === 'paid') {
                return self::RESULT_ALREADY_PAID;
            }

            $payment = $order->payments()
                ->where('gateway', 'moyasar')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $this->amountMatches($order, $remote)) {
                Log::warning('Payment amount mismatch', [
                    'order_id'   => $order->id,
                    'payment_id' => $remote->id,
                    'source'     => $source,
                ]);

                $payment?->update([
                    'transaction_id' => $remote->id,
                    'status'         => Payment::STATUS_FAILED,
                    'raw_response'   => $remote->raw,
                ]);

                return self::RESULT_MISMATCH;
            }

            $payment?->update([
                'transaction_id' => $remote->id,
                'status'         => $remote->localStatus(),
                'raw_response'   => $remote->raw,
                'paid_at'        => $remote->isSuccessful() ? now() : null,
            ]);

            if ($remote->isSuccessful()) {
                $this->confirmOrder($order, $source);

                return self::RESULT_PAID;
            }

            if ($remote->isFailed()) {
                $order->update(['payment_status' => 'failed']);

                return self::RESULT_FAILED;
            }

            return self::RESULT_PENDING;
        });

        if ($result === self::RESULT_PAID) {
            $this->notify($order->fresh());
        }

        return $result;
    }

    private function amountMatches(Order $order, MoyasarPayment $remote): bool
    {
        $expected = (int) round(((float) $order->total) * 100);

        return $remote->amount === $expected
            && $remote->currency === strtoupper((string) $order->currency);
    }

    private function confirmOrder(Order $order, string $source): void
    {
        $previousStatus = $order->status;

        $order->update([
            'payment_status' => 'paid',
            'status'         => 'confirmed',
            'paid_at'        => now(),
        ]);

        OrderStatusLog::create([
            'order_id'    => $order->id,
            'from_status' => $previousStatus,
            'to_status'   => 'confirmed',
            'changed_by'  => $order->user_id,
            'note'        => "Payment verified via Moyasar {$source}.",
        ]);
    }

    private function notify(Order $order): void
    {
        $notification = new OrderPlacedNotification($order);

        $order->user?->notify($notification);

        User::query()
            ->dashboardUsers()
            ->chunkById(100, fn ($admins) => Notification::send($admins, $notification));
    }
}
