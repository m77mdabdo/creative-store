<?php

namespace App\Http\Controllers\MoyasarPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
class MoyasarPaymentController extends Controller
{
    //
     public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $payment = $order->payments()
            ->where('gateway', 'moyasar')
            ->latest()
            ->first();

        if (!$payment) {
            abort(404, 'Payment record not found.');
        }

        return view('site.checkout.payment', [
            'order' => $order,
            'payment' => $payment,
            'publishableKey' => config('services.moyasar.publishable_key'),
            'callbackUrl' => route('checkout.payment.callback'),
        ]);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'id' => ['required', 'string'],
            'status' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
        ]);

        $paymentId = $request->id;

        $response = Http::withBasicAuth(
            config('services.moyasar.secret_key'),
            ''
        )->get("https://api.moyasar.com/v1/payments/{$paymentId}");

        if (!$response->successful()) {

    dd([
        'status_code' => $response->status(),
        'body' => $response->body(),
        'json' => $response->json(),
    ]);

}

        $remotePayment = $response->json();

        $orderId = data_get($remotePayment, 'metadata.order_id');
        $status = data_get($remotePayment, 'status');
        $amount = (int) data_get($remotePayment, 'amount');
        $currency = data_get($remotePayment, 'currency');

        $order = Order::findOrFail($orderId);

        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $payment = $order->payments()
            ->where('gateway', 'moyasar')
            ->latest()
            ->firstOrFail();

        $expectedAmount = (int) round($order->total * 100);

        if ($amount !== $expectedAmount || $currency !== $order->currency) {
            $payment->update([
                'transaction_id' => data_get($remotePayment, 'id'),
                'status' => 'verification_mismatch',
                'raw_response' => $remotePayment,
            ]);

            return redirect()->route('checkout.payment', $order->id)
                ->with('error', 'Payment verification mismatch.');
        }

        $payment->update([
            'transaction_id' => data_get($remotePayment, 'id'),
            'status' => $status,
            'raw_response' => $remotePayment,
            'paid_at' => in_array($status, ['paid', 'captured']) ? now() : null,
        ]);

        if (in_array($status, ['paid', 'captured'])) {
            $oldStatus = $order->status;

            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paid_at' => now(),
            ]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => $oldStatus,
                'to_status' => 'confirmed',
                'changed_by' => $order->user_id,
                'note' => 'Payment verified successfully from Moyasar callback.',
            ]);

            session()->forget('cart');
            session()->forget('coupon');
            session()->forget('shipping');
            session()->forget('checkout_totals');
             $order->user->notify(new OrderPlacedNotification($order));

             $admins = User::all()->filter(function ($user) {
              return $user->isDashboardUser();
                  });

            Notification::send($admins,new OrderPlacedNotification($order));
            return redirect()->route('order.success', $order->id)
                ->with('success', 'Payment completed successfully.');
        }

        $order->update([
            'payment_status' => 'failed',
        ]);

        return redirect()->route('checkout.payment', $order->id)
            ->with('error', 'Payment was not completed.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();

        $log = PaymentWebhookLog::create([
            'gateway' => 'moyasar',
            'event_id' => data_get($payload, 'id'),
            'event_type' => data_get($payload, 'type'),
            'payload' => $payload,
            'processed' => false,
            'processed_at' => null,
        ]);

        if (data_get($payload, 'secret_token') !== config('services.moyasar.webhook_secret')) {
            return response()->json(['message' => 'Invalid webhook secret'], 403);
        }

        $paymentData = data_get($payload, 'data', []);
        $orderId = data_get($paymentData, 'metadata.order_id');
        $status = data_get($paymentData, 'status');

        if ($orderId) {
            $order = Order::find($orderId);

            if ($order) {
                $payment = $order->payments()
                    ->where('gateway', 'moyasar')
                    ->latest()
                    ->first();

                if ($payment) {
                    $payment->update([
                        'transaction_id' => data_get($paymentData, 'id'),
                        'status' => $status,
                        'raw_response' => $paymentData,
                        'paid_at' => in_array($status, ['paid', 'captured']) ? now() : null,
                    ]);
                }

                if (in_array($status, ['paid', 'captured'])) {
                    $oldStatus = $order->status;

                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'paid_at' => now(),
                    ]);

                    OrderStatusLog::create([
                        'order_id' => $order->id,
                        'from_status' => $oldStatus,
                        'to_status' => 'confirmed',
                        'changed_by' => $order->user_id,
                        'note' => 'Payment confirmed by Moyasar webhook.',
                    ]);
                }

                if (in_array($status, ['failed', 'voided'])) {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }
        }

        $log->update([
            'processed' => true,
            'processed_at' => now(),
        ]);

        return response()->json(['message' => 'ok'], 200);
    }

    public function success(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        return view('site.orders.success', compact('order'));
    }
}
