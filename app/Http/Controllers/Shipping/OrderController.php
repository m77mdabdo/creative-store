<?php

namespace App\Http\Controllers\Shipping;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with([
                'shipment',
                'items',
                'shippingMethod',
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
        ]);

        return view('site.shipping.orders.show', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Order already cancelled.');
        }

        if ($order->created_at->diffInHours(now()) >= 3) {
            return back()->with('error', 'Order can no longer be cancelled after 3 hours.');
        }

        $oldStatus = $order->status;

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
            'cancellation_reason' => 'Cancelled by customer',
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'from_status' => $oldStatus,
            'to_status' => 'cancelled',
            'changed_by' => auth()->id(),
            'note' => 'Order cancelled by customer within allowed time.',
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function invoice(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load([
            'items',
            'addressSnapshot',
            'shippingMethod',
            'shipment',
        ]);

        $pdf = Pdf::loadView('site.orders.invoice', [
            'order' => $order,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
