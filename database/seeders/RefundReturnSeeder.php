<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use Illuminate\Database\Seeder;

class RefundReturnSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::query()
            ->with(['items', 'payments'])
            ->whereHas('items')
            ->whereHas('payments')
            ->take(20)
            ->get();

        foreach ($orders as $order) {
            $payment = $order->payments->first();

            if (! $payment) {
                continue;
            }

            $returnRequest = ReturnRequest::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'status' => fake()->randomElement([
                    'pending',
                    'approved',
                    'completed',
                    'rejected',
                ]),
                'reason' => fake()->randomElement([
                    'Damaged product',
                    'Wrong item received',
                    'Size issue',
                    'Customer changed mind',
                ]),
                'refund_shipping' => fake()->boolean(30),
                'refund_tax' => fake()->boolean(50),
                'shipping_refund_amount' => fake()->randomFloat(2, 0, 30),
                'tax_refund_amount' => fake()->randomFloat(2, 0, 20),
                'requested_at' => now()->subDays(rand(1, 20)),
                'processed_at' => fake()->boolean(70) ? now()->subDays(rand(0, 10)) : null,
            ]);

            $items = $order->items->shuffle()->take(rand(1, min(2, $order->items->count())));

            $itemsRefundTotal = 0;

            foreach ($items as $orderItem) {
                $qty = min((int) $orderItem->quantity, rand(1, max(1, (int) $orderItem->quantity)));
                $refundAmount = round($qty * (float) $orderItem->unit_price, 2);

                ReturnItem::create([
                    'return_id' => $returnRequest->id,
                    'order_item_id' => $orderItem->id,
                    'quantity' => $qty,
                    'condition_note' => fake()->randomElement([
                        'Opened but unused',
                        'Box damaged',
                        'Wrong size',
                        'Product defect',
                    ]),
                    'refund_amount' => $refundAmount,
                ]);

                $itemsRefundTotal += $refundAmount;
            }

            if (in_array($returnRequest->status, ['approved', 'completed'])) {
                Refund::create([
                    'payment_id' => $payment->id,
                    'return_id' => $returnRequest->id,
                    'amount' => round(
                        $itemsRefundTotal
                        + (float) $returnRequest->shipping_refund_amount
                        + (float) $returnRequest->tax_refund_amount,
                        2
                    ),
                  'status' => fake()->randomElement([
    'pending',
    'processed',
    'failed',
]),,
                    'transaction_id' => 'RFD-' . strtoupper(fake()->bothify('??######')),
                    'processed_at' => now()->subDays(rand(0, 10)),
                ]);
            }
        }
    }
}
