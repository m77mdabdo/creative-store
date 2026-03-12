@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <div class="bor10 p-lr-40 p-t-30 p-b-40">
                    <h3 class="mtext-111 cl2 p-b-20">Complete Payment</h3>

                    @if(session('error'))
                        <div class="alert alert-danger m-b-20">{{ session('error') }}</div>
                    @endif

                    <div class="mysr-form"></div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="bor10 p-lr-40 p-t-30 p-b-40">
                    <h4 class="mtext-105 cl2 p-b-20">Order Summary</h4>

                    <div class="p-b-10">
                        <strong>Order Number:</strong> {{ $order->order_number }}
                    </div>

                    <div class="p-b-10">
                        <strong>Total:</strong> {{ number_format($order->total, 2) }} {{ $order->currency }}
                    </div>

                    <div class="p-b-10">
                        <strong>Status:</strong> {{ ucfirst($order->payment_status) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.15.0/moyasar.css">
<script src="https://cdn.moyasar.com/mpf/1.15.0/moyasar.js"></script>

<script>
Moyasar.init({
    element: '.mysr-form',
    amount: {{ (int) round($order->total * 100) }},
    currency: '{{ $order->currency }}',
    description: 'Payment for Order {{ $order->order_number }}',
    publishable_api_key: '{{ $publishableKey }}',
    callback_url: '{{ $callbackUrl }}',
    methods: ['creditcard'],
    supported_networks: ['mada', 'visa', 'mastercard'],
    language: 'en',
    metadata: {
        order_id: '{{ $order->id }}',
        order_number: '{{ $order->order_number }}',
        payment_local_id: '{{ $payment->id }}'
    },
    on_failure: function (error) {
        console.error('Moyasar error:', error);
    }
});
</script>
@endsection
