<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RefundService
{
    public function refundOrder(
        Order $order,
        ?float $amount = null,
        ?string $reason = null,
        ?int $returnId = null
    ): Refund {
        $payment = Payment::query()
            ->where('order_id', $order->id)
            ->whereIn('status', ['paid', 'captured'])
            ->latest('id')
            ->first();

        if (! $payment) {
            throw new RuntimeException('No refundable payment found for this order.');
        }

        if (empty($payment->transaction_id)) {
            throw new RuntimeException('Payment transaction ID is missing.');
        }

        $alreadyProcessed = Refund::query()
            ->where('payment_id', $payment->id)
            ->where('status', Refund::STATUS_PROCESSED)
            ->sum('amount');

        $maxRefundable = max(0, (float) $payment->amount - (float) $alreadyProcessed);

        if ($maxRefundable <= 0) {
            throw new RuntimeException('This payment is already fully refunded.');
        }

        $refundAmount = $amount ?? $maxRefundable;

        if ($refundAmount <= 0) {
            throw new RuntimeException('Refund amount must be greater than zero.');
        }

        if ($refundAmount > $maxRefundable) {
            throw new RuntimeException('Refund amount exceeds refundable balance.');
        }

        return DB::transaction(function () use ($order, $payment, $refundAmount, $reason, $maxRefundable, $returnId) {
            $refund = Refund::create([
                'payment_id'     => $payment->id,
                'return_id'      => $returnId,
                'amount'         => $refundAmount,
                'status'         => Refund::STATUS_PENDING,
                'transaction_id' => null,
                'processed_at'   => null,
            ]);

            $payload = [];

            if ($refundAmount < $maxRefundable) {
                $payload['amount'] = (int) round($refundAmount * 100);
            }

            $response = Http::withBasicAuth(
                config('services.moyasar.secret_key'),
                ''
            )->post("https://api.moyasar.com/v1/payments/{$payment->transaction_id}/refund", $payload);

            if (! $response->successful()) {
                $refund->update([
                    'status' => Refund::STATUS_FAILED,
                ]);

                throw new RuntimeException('Refund request failed: ' . $response->body());
            }

            $body = $response->json();

            $refund->update([
                'status'         => Refund::STATUS_PROCESSED,
                'transaction_id' => $body['id'] ?? $payment->transaction_id,
                'processed_at'   => now(),
            ]);

            $processedRefundsTotal = Refund::query()
                ->where('payment_id', $payment->id)
                ->where('status', Refund::STATUS_PROCESSED)
                ->sum('amount');

            $isFullyRefunded = (float) $processedRefundsTotal >= (float) $payment->amount;

            $payment->update([
                'status' => $isFullyRefunded ? 'refunded' : 'partially_refunded',
            ]);

            $order->update([
                'payment_status'      => $isFullyRefunded ? 'refunded' : 'partially_refunded',
                'cancelled_at'        => $order->cancelled_at ?? now(),
                'cancellation_reason' => $reason ?: $order->cancellation_reason,
            ]);

            return $refund;
        });
    }
}
