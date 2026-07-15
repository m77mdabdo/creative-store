<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
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
            ->latest('id')
            ->first();

        if (! $payment) {
            throw new RuntimeException('No payment found for this order.');
        }

        if (empty($payment->transaction_id)) {
            throw new RuntimeException('Payment transaction ID is missing.');
        }

        $processedRefundsTotal = (float) Refund::query()
            ->where('payment_id', $payment->id)
            ->where('status', Refund::STATUS_PROCESSED)
            ->sum('amount');

        $paymentAmount = (float) $payment->amount;
        $targetAmount = (float) ($amount ?? $paymentAmount);
        $maxRefundable = max(0, $paymentAmount - $processedRefundsTotal);

        if (
            $payment->status === 'refunded' ||
            $processedRefundsTotal >= $paymentAmount
        ) {
            $existingRefund = Refund::query()
                ->where('payment_id', $payment->id)
                ->where('status', Refund::STATUS_PROCESSED)
                ->latest('id')
                ->first();

            $payment->update([
                'status' => 'refunded',
            ]);

            $order->update([
                'payment_status'      => 'refunded',
                'cancelled_at'        => $order->cancelled_at ?? now(),
                'cancellation_reason' => $reason ?: $order->cancellation_reason,
            ]);

            if ($existingRefund) {
                return $existingRefund;
            }

            return Refund::create([
                'payment_id'     => $payment->id,
                'return_id'      => $returnId,
                'amount'         => $paymentAmount,
                'status'         => Refund::STATUS_PROCESSED,
                'transaction_id' => $payment->transaction_id,
                'processed_at'   => now(),
            ]);
        }

        if (! in_array($payment->status, ['paid', 'captured', 'partially_refunded'], true)) {
            throw new RuntimeException('This payment is not refundable in its current state.');
        }

        if ($targetAmount <= 0) {
            throw new RuntimeException('Refund amount must be greater than zero.');
        }

        if ($targetAmount > $maxRefundable) {
            throw new RuntimeException('Refund amount exceeds refundable balance.');
        }

        $refund = Refund::create([
            'payment_id'     => $payment->id,
            'return_id'      => $returnId,
            'amount'         => $targetAmount,
            'status'         => Refund::STATUS_PENDING,
            'transaction_id' => null,
            'processed_at'   => null,
        ]);

        $payload = [];

        if ($targetAmount < $paymentAmount) {
            $payload['amount'] = (int) round($targetAmount * 100);
        }

        $response = Http::withBasicAuth(
            config('services.moyasar.secret_key'),
            ''
        )->acceptJson()->timeout(20)->post(
            "https://api.moyasar.com/v1/payments/{$payment->transaction_id}/refund",
            $payload
        );

        if (! $response->successful()) {
            $body = $response->json();
            $message = strtolower((string) ($body['message'] ?? ''));

            if (str_contains($message, 'already refunded')) {
                $refund->update([
                    'status'         => Refund::STATUS_PROCESSED,
                    'transaction_id' => $payment->transaction_id,
                    'processed_at'   => now(),
                ]);

                $payment->update([
                    'status' => 'refunded',
                ]);

                $order->update([
                    'payment_status'      => 'refunded',
                    'cancelled_at'        => $order->cancelled_at ?? now(),
                    'cancellation_reason' => $reason ?: $order->cancellation_reason,
                ]);

                return $refund;
            }

            $refund->update([
                'status' => Refund::STATUS_FAILED,
            ]);

            // Keep gateway details in the log; the message may reach a customer.
            \Illuminate\Support\Facades\Log::error('Moyasar refund failed', [
                'order_id'   => $order->id,
                'payment_id' => $payment->id,
                'status'     => $response->status(),
                'body'       => $response->json(),
            ]);

            throw new RuntimeException(__('The refund could not be processed right now. Our team has been notified.'));
        }

        $body = $response->json();

        $refund->update([
            'status'         => Refund::STATUS_PROCESSED,
            'transaction_id' => $body['id'] ?? $payment->transaction_id,
            'processed_at'   => now(),
        ]);

        $processedRefundsTotal = (float) Refund::query()
            ->where('payment_id', $payment->id)
            ->where('status', Refund::STATUS_PROCESSED)
            ->sum('amount');

        $isFullyRefunded = $processedRefundsTotal >= $paymentAmount;

        $payment->update([
            'status' => $isFullyRefunded ? 'refunded' : 'partially_refunded',
        ]);

        $order->update([
            'payment_status'      => $isFullyRefunded ? 'refunded' : 'partially_refunded',
            'cancelled_at'        => $order->cancelled_at ?? now(),
            'cancellation_reason' => $reason ?: $order->cancellation_reason,
        ]);

        return $refund;
    }
}
