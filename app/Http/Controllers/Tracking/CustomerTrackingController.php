<?php

namespace App\Http\Controllers\Tracking;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\Messaging\Messenger;
use App\Support\Tracking\OrderTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public order tracking. Anyone with a tracking number, or an order number
 * plus the phone number used on it, can follow that order. The search is a
 * POST that redirects to a shareable page (so refreshing never re-submits),
 * and that page opens only for its owner or a browser that proved it knows
 * the order details.
 */
class CustomerTrackingController extends Controller
{
    private const SESSION_KEY = 'tracking.orders';

    public function searchForm(Request $request): View
    {
        $recentOrders = $request->user()
            ? $request->user()->orders()->latest()->take(5)->get(['id', 'order_number', 'status', 'total', 'currency', 'created_at'])
            : collect();

        return view('tracking.search', compact('recentOrders'));
    }

    public function track(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:150'],
            'order_number'    => ['nullable', 'string', 'max:150'],
            'phone'           => ['nullable', 'string', 'max:50'],
        ]);

        $trackingNumber = strtoupper(trim((string) ($data['tracking_number'] ?? '')));
        $orderNumber = strtoupper(trim((string) ($data['order_number'] ?? '')));
        $phone = trim((string) ($data['phone'] ?? ''));

        if ($trackingNumber === '' && ($orderNumber === '' || $phone === '')) {
            return back()->withErrors(['tracking' => __('tracking.need_details')])->withInput();
        }

        $order = $trackingNumber !== ''
            ? Shipment::query()->where('tracking_number', $trackingNumber)->latest('id')->first()?->order
            : $this->orderMatchingPhone($orderNumber, $phone);

        if (! $order) {
            return back()->withErrors(['tracking' => __('tracking.not_found')])->withInput();
        }

        $this->remember($request, $order);

        return redirect()->route('tracking.show', $order->order_number);
    }

    public function show(Request $request, string $orderNumber): View|RedirectResponse
    {
        $order = $this->authorizedOrder($request, $orderNumber);

        if (! $order) {
            return redirect()->route('tracking.search-form')
                ->withErrors(['tracking' => __('tracking.need_details')]);
        }

        return view('tracking.result', ['tracking' => OrderTracking::for($order)]);
    }

    /**
     * Small status payload the open tracking page polls, so it moves on
     * by itself when the order does.
     */
    public function status(Request $request, string $orderNumber): JsonResponse
    {
        $order = $this->authorizedOrder($request, $orderNumber);

        abort_unless($order, 404);

        $tracking = OrderTracking::for($order);

        return response()->json([
            'stage'   => $tracking->currentStage(),
            'status'  => $order->status,
            'updated' => $tracking->events()->first()?->at?->getTimestamp(),
        ]);
    }

    private function orderMatchingPhone(string $orderNumber, string $phone): ?Order
    {
        $wanted = Messenger::normalize($phone);

        if (! $wanted) {
            return null;
        }

        $order = Order::query()
            ->with(['user:id,phone', 'addressSnapshot:id,order_id,phone'])
            ->where('order_number', $orderNumber)
            ->first();

        $known = array_filter([
            $order?->user?->phone,
            $order?->addressSnapshot?->phone,
            $order?->guest_phone,
        ]);

        foreach ($known as $candidate) {
            if (Messenger::normalize($candidate) === $wanted) {
                return $order;
            }
        }

        return null;
    }

    private function authorizedOrder(Request $request, string $orderNumber): ?Order
    {
        $order = Order::query()->where('order_number', $orderNumber)->first();

        if (! $order) {
            return null;
        }

        $user = $request->user();
        $allowed = in_array($order->id, (array) $request->session()->get(self::SESSION_KEY, []), true)
            || ($user && $order->user_id === $user->id)
            || ($user && $user->isDashboardUser());

        return $allowed ? $order : null;
    }

    private function remember(Request $request, Order $order): void
    {
        $ids = (array) $request->session()->get(self::SESSION_KEY, []);
        $ids[] = $order->id;

        $request->session()->put(self::SESSION_KEY, array_slice(array_values(array_unique($ids)), -20));
    }
}
