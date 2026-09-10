<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Order\CancelOrderRequest;
use App\Http\Requests\Dashboard\Order\UpdateOrderStatusesRequest;
use App\Http\Requests\Dashboard\Order\UpsertShipmentRequest;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderStatusLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Coupons\CouponRedemptionService;
use App\Services\Payments\RefundService;
use RuntimeException;

use App\Models\ReturnRequest;
use App\Models\Refund;




class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search'          => trim((string) $request->input('search', '')),
            'status'          => (string) $request->input('status', ''),
            'payment_status'  => (string) $request->input('payment_status', ''),
            'shipping_status' => (string) $request->input('shipping_status', ''),
            'payment_method'  => (string) $request->input('payment_method', ''),
            'date_from'       => (string) $request->input('date_from', ''),
            'date_to'         => (string) $request->input('date_to', ''),
        ];

        $query = Order::query()
            ->with([
                'user:id,name,email,phone',
                'shippingMethod:id,name,code',
                'items:id,order_id,quantity,line_total',
            ])
            ->withCount('items');

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhere('guest_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['payment_status'] !== '') {
            $query->where('payment_status', $filters['payment_status']);
        }

        if ($filters['shipping_status'] !== '') {
            $query->where('shipping_status', $filters['shipping_status']);
        }

        if ($filters['payment_method'] !== '') {
            $query->where('payment_method', $filters['payment_method']);
        }

        if ($filters['date_from'] !== '') {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $orders = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'totalOrders' => Order::count(),
            'paidOrders' => Order::where('payment_status', 'paid')->count(),
            'pendingOrders' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'cancelledOrders' => Order::where('status', 'cancelled')->count(),
            'totalSales' => (float) Order::where('payment_status', 'paid')->sum('total'),
        ];

        return view('dashboard.orders.index', [
            'orders' => $orders,
            'filters' => $filters,
            'stats' => $stats,
            'canView' => $this->canCurrentUser('orders.view'),
            'canEdit' => $this->canCurrentUser('orders.edit'),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'user:id,name,email,phone',
            'shippingAddress',
            'shippingMethod:id,name,code,description',
            'addressSnapshot',
            'items.product:id,slug,sku',
            'items.variant:id,product_id,sku,stock',
            'statusLogs.changer:id,name,email',
            'payments' => fn ($q) => $q->latest('id'),
            'shipment.method:id,name,code',
            'coupon:id,code,type,value',
            'couponUsage',
            'returnRequests.items.orderItem',
            'returnRequests.refunds.payment',
        ]);

        return view('dashboard.orders.show', [
            'order' => $order,
            'canView' => $this->canCurrentUser('orders.view'),
            'canEdit' => $this->canCurrentUser('orders.edit'),
        ]);
    }

    public function updateStatuses(UpdateOrderStatusesRequest $request, Order $order): RedirectResponse
    {
        $newStatus = (string) $request->input('status');
        $newPaymentStatus = (string) $request->input('payment_status');
        $newShippingStatus = (string) $request->input('shipping_status');
        $note = trim((string) $request->input('note', ''));

        $oldStatus = (string) $order->status;
        $oldPaymentStatus = (string) $order->payment_status;
        $oldShippingStatus = (string) $order->shipping_status;

        if (
            $oldStatus === $newStatus &&
            $oldPaymentStatus === $newPaymentStatus &&
            $oldShippingStatus === $newShippingStatus
        ) {
            return back()->with('error', __('No status changes were made.'));
        }

        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            return back()->with('error', __('Cancelled orders cannot be reopened from this action.'));
        }

        if ($oldShippingStatus === 'delivered' && $newShippingStatus !== 'delivered') {
            return back()->with('error', __('Delivered shipment status cannot be rolled back from this action.'));
        }

        if ($newStatus === 'completed' && $newPaymentStatus !== 'paid') {
            return back()->with('error', __('Order cannot be marked as completed unless payment status is paid.'));
        }

        if ($newShippingStatus === 'delivered' && !in_array($newStatus, ['processing', 'completed'], true)) {
            return back()->with('error', __('Shipping cannot be marked delivered unless order is processing or completed.'));
        }

        DB::transaction(function () use (
            $order,
            $newStatus,
            $newPaymentStatus,
            $newShippingStatus,
            $note,
            $oldStatus,
            $oldPaymentStatus,
            $oldShippingStatus
        ) {
            $updateData = [
                'status' => $newStatus,
                'payment_status' => $newPaymentStatus,
                'shipping_status' => $newShippingStatus,
            ];

            if ($oldPaymentStatus !== 'paid' && $newPaymentStatus === 'paid' && is_null($order->paid_at)) {
                $updateData['paid_at'] = now();
            }

            if ($newStatus === 'cancelled' && is_null($order->cancelled_at)) {
                $updateData['cancelled_at'] = now();
                $updateData['cancelled_by'] = auth()->id();
                $updateData['cancellation_reason'] = $note !== '' ? $note : $order->cancellation_reason;
            }

            $order->update($updateData);

            if ($oldStatus !== $newStatus) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => $oldStatus,
                    'to_status' => $newStatus,
                    'changed_by' => auth()->id(),
                    'note' => $this->buildStatusLogNote('Order status', $note, $oldStatus, $newStatus),
                ]);
            }

            if ($oldPaymentStatus !== $newPaymentStatus) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => 'payment:' . $oldPaymentStatus,
                    'to_status' => 'payment:' . $newPaymentStatus,
                    'changed_by' => auth()->id(),
                    'note' => $this->buildStatusLogNote('Payment status', $note, $oldPaymentStatus, $newPaymentStatus),
                ]);
            }

            if ($oldShippingStatus !== $newShippingStatus) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => 'shipping:' . $oldShippingStatus,
                    'to_status' => 'shipping:' . $newShippingStatus,
                    'changed_by' => auth()->id(),
                    'note' => $this->buildStatusLogNote('Shipping status', $note, $oldShippingStatus, $newShippingStatus),
                ]);
            }
        });

        return back()->with('success', __('Order statuses updated successfully.'));
    }

    public function quickAction(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:mark_processing,mark_paid,mark_completed,mark_shipped,mark_delivered'],
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', __('Cancelled orders cannot be updated by quick actions.'));
        }

        $action = (string) $request->input('action');

        return match ($action) {
            'mark_processing' => $this->applyQuickAction(
                order: $order,
                status: 'processing',
                paymentStatus: $order->payment_status,
                shippingStatus: $order->shipping_status,
                note: 'Quick action: order marked as processing.'
            ),
            'mark_paid' => $this->applyQuickAction(
                order: $order,
                status: $order->status === 'pending' ? 'processing' : $order->status,
                paymentStatus: 'paid',
                shippingStatus: $order->shipping_status,
                note: 'Quick action: payment marked as paid.'
            ),
            'mark_completed' => $this->applyQuickAction(
                order: $order,
                status: 'completed',
                paymentStatus: 'paid',
                shippingStatus: $order->shipping_status,
                note: 'Quick action: order marked as completed.'
            ),
            'mark_shipped' => $this->applyQuickAction(
                order: $order,
                status: in_array($order->status, ['pending', 'processing'], true) ? 'processing' : $order->status,
                paymentStatus: $order->payment_status,
                shippingStatus: 'shipped',
                note: 'Quick action: shipment marked as shipped.'
            ),
            'mark_delivered' => $this->applyQuickAction(
                order: $order,
                status: $order->payment_status === 'paid' ? 'completed' : 'processing',
                paymentStatus: $order->payment_status === 'pending' ? 'paid' : $order->payment_status,
                shippingStatus: 'delivered',
                note: 'Quick action: shipment marked as delivered.'
            ),
            default => back()->with('error', __('Invalid quick action.')),
        };
    }

    public function upsertShipment(UpsertShipmentRequest $request, Order $order): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', __('Cannot manage shipment for a cancelled order.'));
        }

        $data = [
            'shipping_method_id' => $order->shipping_method_id,
            'carrier' => trim((string) $request->input('carrier', '')),
            'tracking_number' => trim((string) $request->input('tracking_number', '')),
            'status' => (string) $request->input('status'),
            'current_location' => trim((string) $request->input('current_location', '')),
            'carrier_status' => trim((string) $request->input('carrier_status', '')),
            'shipped_at' => $request->filled('shipped_at') ? $request->input('shipped_at') : null,
            'delivered_at' => $request->filled('delivered_at') ? $request->input('delivered_at') : null,
            'notes' => trim((string) $request->input('notes', '')),
        ];

        DB::transaction(function () use ($order, $data) {
            $shipment = $order->shipment()->updateOrCreate(
                ['order_id' => $order->id],
                $data
            );

            $oldShippingStatus = (string) $order->shipping_status;
            $newShippingStatus = (string) $shipment->status;

            $orderUpdate = [
                'shipping_status' => $newShippingStatus,
            ];

            if ($newShippingStatus === 'shipped' && $order->status === 'pending') {
                $orderUpdate['status'] = 'processing';
            }

            if ($newShippingStatus === 'delivered') {
                if ($order->payment_status === 'paid') {
                    $orderUpdate['status'] = 'completed';
                } elseif ($order->status === 'pending') {
                    $orderUpdate['status'] = 'processing';
                }
            }

            $order->update($orderUpdate);

            if ($oldShippingStatus !== $newShippingStatus) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => 'shipping:' . $oldShippingStatus,
                    'to_status' => 'shipping:' . $newShippingStatus,
                    'changed_by' => auth()->id(),
                    'note' => 'Shipment record updated from dashboard.',
                ]);
            }
        });

        return back()->with('success', __('Shipment information saved successfully.'));
    }



public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
{
    if ($order->status === 'cancelled') {
        return back()->with('error', __('Order is already cancelled.'));
    }

    $reason = trim((string) $request->input('reason', 'Order cancelled by admin'));
    $refundIfPaid = (bool) $request->boolean('refund_if_paid');
    $refundAmount = $request->filled('refund_amount')
        ? (float) $request->input('refund_amount')
        : null;

    try {
        DB::transaction(function () use (&$order, $reason, $refundIfPaid, $refundAmount) {
            // Re-read under lock so a second click cannot cancel (and restock) twice.
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->status === 'cancelled') {
                throw new RuntimeException(__('Order is already cancelled.'));
            }

            $order->load([
                'items',
                'payments' => fn ($q) => $q->latest('id'),
            ]);

            $previousStatus = $order->status;

            // 1) Cancel order
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
                'cancellation_reason' => $reason,
            ]);

            // 2) Log status change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => $previousStatus,
                'to_status' => 'cancelled',
                'changed_by' => auth()->id(),
                'note' => 'Order cancelled from dashboard. Reason: ' . $reason,
            ]);

            // 3) Give back the coupon uses this order consumed (stock is restored in step 6)
            app(CouponRedemptionService::class)->release($order);

            // 4) Create return request
            $returnRequest = ReturnRequest::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'status' => ReturnRequest::STATUS_APPROVED,
                'reason' => $reason,
                'refund_shipping' => true,
                'refund_tax' => true,
                'shipping_refund_amount' => (float) ($order->shipping_fee ?? 0),
                'tax_refund_amount' => (float) ($order->tax_total ?? 0),
                'requested_at' => now(),
                'processed_at' => now(),
            ]);

            // 4) Create return items for full order cancellation
            foreach ($order->items as $item) {
                \App\Models\ReturnItem::create([
                    'return_id' => $returnRequest->id,
                    'order_item_id' => $item->id,
                    'quantity' => (int) $item->quantity,
                    'condition_note' => 'Cancelled before completion',
                    'refund_amount' => (float) $item->line_total,
                ]);
            }

            // 5) Refund if paid and requested
            $paidPayment = $order->payments
                ->first(fn ($payment) => in_array($payment->status, ['paid', 'captured'], true));

            if ($refundIfPaid && $paidPayment) {
                $refund = app(RefundService::class)->refundOrder(
                    order: $order,
                    amount: $refundAmount,
                    reason: $reason,
                    returnId: $returnRequest->id
                );

                $returnRequest->update([
                    'status' => ReturnRequest::STATUS_COMPLETED,
                    'processed_at' => now(),
                ]);

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => 'payment:' . ($order->getOriginal('payment_status') ?? 'paid'),
                    'to_status' => 'payment:' . $order->fresh()->payment_status,
                    'changed_by' => auth()->id(),
                    'note' => 'Refund created successfully. Refund ID: ' . $refund->id,
                ]);
            } else {
                $order->update([
                    'payment_status' => $paidPayment ? 'refund_pending' : 'cancelled',
                ]);
            }

            // 6) Optional: return stock back
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    DB::table('product_variants')
                        ->where('id', $item->variant_id)
                        ->increment('stock', (int) $item->quantity);
                }

                InventoryMovement::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type' => 'restock',
                    'quantity' => (int) $item->quantity,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'note' => 'Stock restored after order cancellation.',
                ]);
            }
        });

        return back()->with('success', __('Order cancelled successfully and return/refund recorded.'));
    } catch (\Throwable $e) {
        report($e);

        return back()->with('error', $e->getMessage() ?: 'Failed to cancel order.');
    }
}

    public function invoice(Order $order, \App\Services\Invoices\TaxInvoiceBuilder $invoices): Response
    {
        $pdf = Pdf::loadView('invoices.tax-invoice', $invoices->build($order))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . $order->order_number . '.pdf');
    }

    private function applyQuickAction(
        Order $order,
        string $status,
        string $paymentStatus,
        string $shippingStatus,
        string $note
    ): RedirectResponse {
        $oldStatus = (string) $order->status;
        $oldPaymentStatus = (string) $order->payment_status;
        $oldShippingStatus = (string) $order->shipping_status;

        if ($oldShippingStatus === 'delivered' && $shippingStatus !== 'delivered') {
            return back()->with('error', __('Delivered shipment status cannot be rolled back.'));
        }

        if ($status === 'completed' && $paymentStatus !== 'paid') {
            return back()->with('error', __('Completed orders must be paid.'));
        }

        DB::transaction(function () use (
            $order,
            $status,
            $paymentStatus,
            $shippingStatus,
            $note,
            $oldStatus,
            $oldPaymentStatus,
            $oldShippingStatus
        ) {
            $updateData = [
                'status' => $status,
                'payment_status' => $paymentStatus,
                'shipping_status' => $shippingStatus,
            ];

            if ($oldPaymentStatus !== 'paid' && $paymentStatus === 'paid' && is_null($order->paid_at)) {
                $updateData['paid_at'] = now();
            }

            $order->update($updateData);

            if ($oldStatus !== $status) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => $oldStatus,
                    'to_status' => $status,
                    'changed_by' => auth()->id(),
                    'note' => $note,
                ]);
            }

            if ($oldPaymentStatus !== $paymentStatus) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => 'payment:' . $oldPaymentStatus,
                    'to_status' => 'payment:' . $paymentStatus,
                    'changed_by' => auth()->id(),
                    'note' => $note,
                ]);
            }

            if ($oldShippingStatus !== $shippingStatus) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'from_status' => 'shipping:' . $oldShippingStatus,
                    'to_status' => 'shipping:' . $shippingStatus,
                    'changed_by' => auth()->id(),
                    'note' => $note,
                ]);
            }
        });

        return back()->with('success', __('Quick action applied successfully.'));
    }

    private function buildStatusLogNote(string $label, string $note, string $from, string $to): string
    {
        $base = $label . " changed from {$from} to {$to}.";

        if ($note !== '') {
            return $base . ' Note: ' . $note;
        }

        return $base;
    }

    private function canCurrentUser(string $permission): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('owner')
            || $user->hasRole('admin')
            || $user->hasPermission($permission);
    }
}
