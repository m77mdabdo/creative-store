@extends('site.app.layout')

@section('title', __('site.payment'))

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-checkout-payment.css') }}">

<section class="payment-page">
    <div class="container">

        <div class="payment-page-title">
            <h1>{{ __('site.checkout') }}</h1>
            <p>{{ __('site.payment_page_subtitle') }}</p>
        </div>

        <div class="row">
            <div class="col-lg-7 p-b-30">
                <div class="payment-card">
                    <div class="payment-card-header">
                        <h3 class="payment-card-title">{{ __('site.complete_payment') }}</h3>
                        <p class="payment-card-subtitle">
                            {{ __('site.complete_payment_subtitle') }}
                        </p>
                    </div>

                    <div class="payment-card-body">
                        <div class="payment-secure-badge">
                            <i class="fa fa-lock"></i>
                            <span>{{ __('site.secure_payment_powered_by_moyasar') }}</span>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger payment-alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="payment-form-shell">
                            <div class="mysr-form"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 p-b-30">
                <div class="payment-card">
                    <div class="payment-card-header">
                        <h4 class="payment-card-title">{{ __('site.order_summary') }}</h4>
                        <p class="payment-card-subtitle">
                            {{ __('site.order_summary_subtitle') }}
                        </p>
                    </div>

                    <div class="payment-card-body">
                        <div class="order-summary-list">

                            <div class="order-summary-item">
                                <span class="order-summary-label">{{ __('site.order_number') }}</span>
                                <span class="order-summary-value">{{ $order->order_number }}</span>
                            </div>

                            <div class="order-summary-item">
                                <span class="order-summary-label">{{ __('site.currency') }}</span>
                                <span class="order-summary-value">{{ $order->currency }}</span>
                            </div>

                            <div class="order-summary-item">
                                <span class="order-summary-label">{{ __('site.payment_status') }}</span>
                                <span class="order-summary-value">
                                    <span class="payment-status-badge">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </span>
                            </div>

                        </div>

                        <div class="payment-summary-total">
                            <div class="payment-summary-total-box">
                                <span class="label">{{ __('site.total_amount') }}</span>
                                <span class="value">
                                    {{ money($order->total, $order->currency) }}
                                </span>
                            </div>
                        </div>

                        <div class="payment-note">
                            {{ __('site.payment_note') }}
                        </div>
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
    language: '{{ app()->getLocale() === "ar" ? "ar" : "en" }}',
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
