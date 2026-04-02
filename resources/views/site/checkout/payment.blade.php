@extends('site.app.layout')

@section('content')

<style>
    .payment-page {
        padding-top: 70px;
        padding-bottom: 90px;
        background: #fff;
    }

    .payment-page-title {
        margin-bottom: 28px;
        text-align: center;
    }

    .payment-page-title h1 {
        font-size: 40px;
        line-height: 1.2;
        font-weight: 800;
        color: #222;
        margin-bottom: 10px;
    }

    .payment-page-title p {
        font-size: 15px;
        color: #777;
        margin-bottom: 0;
    }

    .payment-card {
        background: #fff;
        border: 1px solid #efefef;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
        overflow: hidden;
        height: 100%;
        transition: all .3s ease;
    }

    .payment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(0,0,0,.08);
    }

    .payment-card-header {
        padding: 22px 24px 16px;
        border-bottom: 1px solid #f2f2f2;
        background: linear-gradient(180deg, #ffffff, #fcfcfc);
    }

    .payment-card-title {
        font-size: 26px;
        line-height: 1.3;
        font-weight: 800;
        color: #222;
        margin-bottom: 6px;
    }

    .payment-card-subtitle {
        font-size: 14px;
        color: #777;
        margin-bottom: 0;
    }

    .payment-card-body {
        padding: 24px;
    }

    .payment-alert {
        border-radius: 16px;
        margin-bottom: 18px;
    }

    .payment-form-shell {
        border: 1px solid #ececec;
        border-radius: 20px;
        background: #fafafa;
        padding: 18px;
        min-height: 220px;
    }

    .mysr-form {
        width: 100%;
    }

    .order-summary-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .order-summary-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #fafafa;
        border: 1px solid #efefef;
    }

    .order-summary-label {
        font-size: 14px;
        color: #666;
        font-weight: 600;
    }

    .order-summary-value {
        font-size: 16px;
        color: #222;
        font-weight: 700;
        text-align: right;
    }

    .payment-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(113,127,224,.10);
        color: #717fe0;
        font-size: 13px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .payment-summary-total {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #f1f1f1;
    }

    .payment-summary-total-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 18px;
        border-radius: 18px;
        background: #222;
        color: #fff;
        box-shadow: 0 14px 30px rgba(0,0,0,.12);
    }

    .payment-summary-total-box .label {
        font-size: 15px;
        font-weight: 600;
        opacity: .92;
    }

    .payment-summary-total-box .value {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
    }

    .payment-note {
        margin-top: 18px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #fff8e8;
        border: 1px solid #f4e1a7;
        color: #8a6d3b;
        font-size: 14px;
        line-height: 1.8;
    }

    .payment-secure-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(56,161,105,.10);
        color: #2f855a;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .payment-secure-badge i {
        font-size: 15px;
    }

    html[dir="rtl"] .payment-page,
    html[dir="rtl"] .payment-card-header,
    html[dir="rtl"] .payment-card-body,
    html[dir="rtl"] .payment-note,
    html[dir="rtl"] .payment-page-title {
        text-align: right;
    }

    html[dir="rtl"] .payment-page-title {
        text-align: right;
    }

    html[dir="rtl"] .order-summary-item,
    html[dir="rtl"] .payment-summary-total-box,
    html[dir="rtl"] .payment-secure-badge {
        flex-direction: row-reverse;
    }

    html[dir="rtl"] .order-summary-value {
        text-align: left;
    }

    html[dir="rtl"] .payment-form-shell {
        text-align: right;
    }

    @media (max-width: 991px) {
        .payment-page {
            padding-top: 45px;
            padding-bottom: 70px;
        }

        .payment-page-title h1 {
            font-size: 32px;
        }

        .payment-card-title {
            font-size: 22px;
        }
    }

    @media (max-width: 767px) {
        .payment-page-title h1 {
            font-size: 28px;
        }

        .payment-card-header,
        .payment-card-body {
            padding: 18px 16px;
        }

        .payment-form-shell {
            padding: 12px;
        }

        .order-summary-item,
        .payment-summary-total-box {
            align-items: flex-start;
        }
    }

    /* إخفاء رسالة Test Mode الخاصة بـ Moyasar */
    .mysr-form .mysr-alert {
        display: none !important;
    }
</style>

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
                                    {{ number_format($order->total, 2) }} {{ $order->currency }}
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
