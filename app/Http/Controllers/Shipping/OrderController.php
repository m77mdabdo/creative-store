<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderCancelledNotificationJob;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\ReturnRequest;
use App\Services\Coupons\CouponRedemptionService;
use App\Services\Payments\RefundService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(
        protected RefundService $refundService,
        protected CouponRedemptionService $coupons,
    ) {}

    public function index(): View
    {
        $orders = Order::query()
            ->with([
                'shipment',
                'items',
                'shippingMethod',
                'latestPayment',
            ])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('site.shipping.orders.index', compact('orders'));
    }

    public function show(Order $order): View|RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load([
            'items',
            'shipment',
            'addressSnapshot',
            'shippingMethod',
            'statusLogs',
            'latestPayment',
        ]);

        return view('site.shipping.orders.show', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->status === 'cancelled') {
            return back()->with('error', __('Order already cancelled.'));
        }

        if (! in_array($order->shipping_status, ['pending', 'processing', null], true)) {
            return back()->with('error', __('This order cannot be cancelled after shipping has started.'));
        }

        if (! $order->created_at || $order->created_at->diffInHours(now()) >= 3) {
            return back()->with('error', __('Order can no longer be cancelled after 3 hours.'));
        }

        try {
            $wasPaidOnline = false;

            DB::transaction(function () use (&$order, &$wasPaidOnline) {
                // Re-read under lock: two cancel clicks must not restore stock twice.
                $order = Order::query()->lockForUpdate()->findOrFail($order->id);

                if ($order->status === 'cancelled') {
                    throw new RuntimeException('Order already cancelled.');
                }

                $order->loadMissing([
                    'items.variant',
                    'latestPayment',
                    'user',
                ]);

                $oldStatus = $order->status;
                $userId = Auth::id();
                $payment = $order->latestPayment;

                $wasPaid = $order->payment_status === 'paid'
                    || in_array(optional($payment)->status, ['paid', 'captured', 'partially_refunded'], true);

                $alreadyRefunded = in_array($order->payment_status, ['refunded'], true)
                    || in_array(optional($payment)->status, ['refunded'], true);

                $wasPaidOnline = $wasPaid && filled(optional($payment)->transaction_id);

                $nextPaymentStatus = $order->payment_status;

                if ($alreadyRefunded) {
                    $nextPaymentStatus = 'refunded';
                } elseif ($wasPaid) {
                    $nextPaymentStatus = 'paid';
                } else {
                    $nextPaymentStatus = 'cancelled';
                }

                $order->update([
                    'status'              => 'cancelled',
                    'shipping_status'     => 'cancelled',
                    'payment_status'      => $nextPaymentStatus,
                    'cancelled_at'        => now(),
                    'cancelled_by'        => $userId,
                    'cancellation_reason' => 'Cancelled by customer',
                ]);

                foreach ($order->items as $item) {
                    if ($item->variant_id && $item->variant) {
                        $item->variant->increment('stock', (int) $item->quantity);
                    }
                }

                $this->coupons->release($order);

                OrderStatusLog::create([
                    'order_id'    => $order->id,
                    'from_status' => $oldStatus,
                    'to_status'   => 'cancelled',
                    'changed_by'  => $userId,
                    'note'        => 'Order cancelled by customer within allowed time.',
                ]);

                $returnRequest = ReturnRequest::firstOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id'                => $order->user_id,
                        'status'                 => ReturnRequest::STATUS_APPROVED,
                        'reason'                 => 'Order cancelled by customer before shipment.',
                        'refund_shipping'        => true,
                        'refund_tax'             => true,
                        'shipping_refund_amount' => (float) ($order->shipping_fee ?? 0),
                        'tax_refund_amount'      => (float) ($order->tax_total ?? 0),
                        'requested_at'           => now(),
                        'processed_at'           => now(),
                    ]
                );

                foreach ($order->items as $item) {
                    $refundAmount = (float) (
                        $item->line_total
                        ?? $item->total
                        ?? (($item->price ?? 0) * ($item->quantity ?? 0))
                    );

                    $returnRequest->items()->firstOrCreate(
                        [
                            'return_id'     => $returnRequest->id,
                            'order_item_id' => $item->id,
                        ],
                        [
                            'quantity'       => (int) $item->quantity,
                            'condition_note' => 'Cancelled before shipment',
                            'refund_amount'  => $refundAmount,
                        ]
                    );
                }

                if ($wasPaid && ! $alreadyRefunded) {
                    $this->refundService->refundOrder(
                        order: $order,
                        amount: (float) $returnRequest->grand_refund_total,
                        reason: 'Order cancelled by customer before shipment.',
                        returnId: $returnRequest->id
                    );

                    $order->refresh();

                    $returnRequest->update([
                        'status'       => $order->payment_status === 'partially_refunded'
                            ? ReturnRequest::STATUS_PARTIAL_REFUNDED
                            : ReturnRequest::STATUS_REFUNDED,
                        'processed_at' => now(),
                    ]);
                } elseif ($alreadyRefunded) {
                    $returnRequest->update([
                        'status'       => ReturnRequest::STATUS_REFUNDED,
                        'processed_at' => now(),
                    ]);
                } else {
                    $returnRequest->update([
                        'status'       => ReturnRequest::STATUS_APPROVED,
                        'processed_at' => now(),
                    ]);
                }
            });

            SendOrderCancelledNotificationJob::dispatch($order->id, $wasPaidOnline);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', __('Unable to cancel order right now. Please try again.'));
        }

        return back()->with('success', __('Order cancelled successfully.'));
    }

    public function invoice(Order $order, \App\Services\Invoices\TaxInvoiceBuilder $invoices)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $pdf = Pdf::loadView('invoices.tax-invoice', $invoices->build($order))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
