@extends('site.app.layout')

@section('title', __('site.order_placed'))

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-orders-success.css') }}">

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
                        {{ money($order->total, $order->currency) }}
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
