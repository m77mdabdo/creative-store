@extends('site.app.layout')

@section('content')

<style>
    .success-page {
        padding-top: 80px;
        padding-bottom: 90px;
        background: #fff;
    }

    .success-card {
        max-width: 600px;
        margin: auto;
        border: 1px solid #efefef;
        border-radius: 24px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
        transition: all .3s ease;
    }

    .success-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(0,0,0,.08);
    }

    .success-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #e6f7f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .success-icon i {
        font-size: 36px;
        color: #28a745;
    }

    .success-title {
        font-size: 28px;
        font-weight: 800;
        color: #222;
        margin-bottom: 10px;
    }

    .success-desc {
        font-size: 15px;
        color: #777;
        margin-bottom: 25px;
    }

    .success-info {
        background: #fafafa;
        border: 1px solid #eee;
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 25px;
        text-align: left;
    }

    .success-info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .success-info-item:last-child {
        margin-bottom: 0;
    }

    .success-label {
        color: #666;
    }

    .success-value {
        font-weight: 700;
        color: #222;
    }

    .success-btn {
        min-height: 46px;
        border-radius: 999px;
        padding: 0 28px;
    }

    html[dir="rtl"] .success-info {
        text-align: right;
    }

    html[dir="rtl"] .success-info-item {
        flex-direction: row-reverse;
    }

</style>

<section class="success-page">
    <div class="container">

        <div class="success-card">

            <!-- ICON -->
            <div class="success-icon">
                <i class="fa fa-check"></i>
            </div>

            <!-- TITLE -->
            <h3 class="success-title">
                {{ __('site.payment_successful') }}
            </h3>

            <!-- MESSAGE -->
            @if(session('success'))
                <div class="alert alert-success m-b-20">
                    {{ session('success') }}
                </div>
            @endif

            <p class="success-desc">
                {{ __('site.thank_you_for_order') }}
            </p>

            <!-- INFO -->
            <div class="success-info">

                <div class="success-info-item">
                    <span class="success-label">{{ __('site.order_number') }}</span>
                    <span class="success-value">{{ $order->order_number }}</span>
                </div>

                <div class="success-info-item">
                    <span class="success-label">{{ __('site.payment_status') }}</span>
                    <span class="success-value">{{ ucfirst($order->payment_status) }}</span>
                </div>

                <div class="success-info-item">
                    <span class="success-label">{{ __('site.total_amount') }}</span>
                    <span class="success-value">
                        {{ number_format($order->total, 2) }} {{ $order->currency }}
                    </span>
                </div>

            </div>

            <!-- BUTTON -->
            <a href="{{ url('/') }}"
               class="success-btn flex-c-m stext-101 cl0 bg3 bor14 hov-btn3 trans-04 pointer mx-auto">
                {{ __('site.continue_shopping') }}
            </a>

        </div>

    </div>
</section>

@endsection
