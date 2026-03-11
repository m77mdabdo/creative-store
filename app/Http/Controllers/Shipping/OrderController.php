<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['shipment', 'items'])
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
}
