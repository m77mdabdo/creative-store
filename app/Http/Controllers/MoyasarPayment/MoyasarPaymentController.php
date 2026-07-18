<?php

namespace App\Http\Controllers\MoyasarPayment;

use App\Exceptions\PaymentGatewayException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentWebhookLog;
use App\Services\Payments\Moyasar\MoyasarGateway;
use App\Services\Payments\Moyasar\MoyasarPayment;
use App\Services\Payments\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoyasarPaymentController extends Controller
{
    private const CHECKOUT_SESSION_KEYS = ['cart', 'coupon', 'shipping', 'checkout_totals'];

    public function __construct(
        private readonly OrderPaymentService $payments,
    ) {
    }

    public function show(Request $request, Order $order): View|RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        // Only an open, unpaid order can be paid; anything else would take
        // money for an order that is already settled or cancelled.
        if ($order->status !== 'pending' || $order->payment_status === 'paid') {
            return redirect()->route('shipping.orders.show', $order)
                ->with('error', __('This order can no longer be paid.'));
        }

        $payment = $order->payments()
            ->where('gateway', 'moyasar')
            ->latest('id')
            ->firstOrFail();

        return view('site.checkout.payment', [
            'order'          => $order,
            'payment'        => $payment,
            'publishableKey' => config('services.moyasar.publishable_key'),
            'callbackUrl'    => route('checkout.payment.callback'),
        ]);
    }

    public function callback(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'max:64'],
        ]);

        try {
            $remote = MoyasarGateway::fromConfig()->fetchPayment($validated['id']);
        } catch (PaymentGatewayException) {
            return redirect()->route('cart.index')
                ->with('error', __('We could not verify your payment. Please contact support if you were charged.'));
        }

        $order = Order::query()->findOrFail($remote->orderId);
        $this->authorizeOwner($request, $order);

        $result = $this->payments->apply($order, $remote, 'callback');

        return match ($result) {
            OrderPaymentService::RESULT_PAID,
            OrderPaymentService::RESULT_ALREADY_PAID => $this->completeCheckout($request, $order),
            OrderPaymentService::RESULT_MISMATCH     => redirect()->route('checkout.payment', $order)
                ->with('error', __('Payment verification failed.')),
            default                                  => redirect()->route('checkout.payment', $order)
                ->with('error', __('Payment was not completed.')),
        };
    }

    public function webhook(Request $request): JsonResponse
    {
        $gateway = MoyasarGateway::fromConfig();

        if (! $gateway->hasValidWebhookSecret($request->input('secret_token'))) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payload = $request->except('secret_token');

        // Moyasar retries a webhook until it gets a 2xx. The (gateway, event_id)
        // unique index makes redelivery idempotent: a processed event is acknowledged
        // without running again, an unprocessed one is retried.
        $log = PaymentWebhookLog::query()->createOrFirst(
            ['gateway' => 'moyasar', 'event_id' => data_get($payload, 'id')],
            ['event_type' => data_get($payload, 'type'), 'payload' => $payload, 'processed' => false],
        );

        if ($log->processed) {
            return response()->json(['message' => 'already processed']);
        }

        $remote = MoyasarPayment::fromArray((array) data_get($payload, 'data', []));

        if ($remote->orderId && $order = Order::query()->find($remote->orderId)) {
            $this->payments->apply($order, $remote, 'webhook');
        }

        $log->update(['processed' => true, 'processed_at' => now()]);

        return response()->json(['message' => 'ok']);
    }

    public function success(Request $request, Order $order): View
    {
        $this->authorizeOwner($request, $order);

        return view('site.orders.success', compact('order'));
    }

    private function completeCheckout(Request $request, Order $order): RedirectResponse
    {
        $request->session()->forget(self::CHECKOUT_SESSION_KEYS);

        return redirect()->route('order.success', $order)
            ->with('success', __('Payment completed successfully.'));
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless((int) $order->user_id === (int) $request->user()?->id, 403);
    }
}
