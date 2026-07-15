@extends('site.app.layout')

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-shipping-orders-index.css') }}">

<section class="orders-page">
    <div class="container">

        <h3 class="orders-title">{{ __('site.my_orders') }}</h3>

        @if(session('success'))
            <div class="alert alert-success m-b-20">{{ session('success') }}</div>
        @endif

        <div class="orders-card">
            @forelse($orders as $order)
                <div class="order-item">
                    <h5 class="order-number">
                        {{ __('site.order_number') }} #{{ $order->order_number }}
                    </h5>

                    <p class="order-meta">
                        <strong>{{ __('site.status') }}:</strong> {{ \Illuminate\Support\Facades\Lang::has('site.status_' . $order->status) ? __('site.status_' . $order->status) : $order->status }}
                    </p>

                    <p class="order-meta">
                        <strong>{{ __('site.shipping_status') }}:</strong> {{ \Illuminate\Support\Facades\Lang::has('site.status_' . $order->shipping_status) ? __('site.status_' . $order->shipping_status) : $order->shipping_status }}
                    </p>

                    <p class="order-meta">
                        <strong>{{ __('site.total') }}:</strong>
                        {{ number_format($order->total, 2) }} SAR
                    </p>

                    <div class="order-actions">
                        <a href="{{ route('shipping.orders.show', $order) }}" class="btn btn-sm btn-dark">
                            {{ __('site.view_order') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-orders">
                    <p>{{ __('site.no_orders_found') }}</p>
                </div>
            @endforelse

            {{ $orders->links() }}
        </div>

    </div>
</section>

@endsection
