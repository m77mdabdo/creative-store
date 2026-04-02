@extends('site.app.layout')

@section('content')

<style>
    .orders-page {
        padding-top: 70px;
        padding-bottom: 90px;
        background: #fff;
    }

    .orders-title {
        font-size: 30px;
        font-weight: 800;
        color: #222;
        margin-bottom: 30px;
    }

    .orders-card {
        border: 1px solid #eee;
        border-radius: 24px;
        padding: 28px;
        background: #fff;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
    }

    .order-item {
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .order-item:last-child {
        border-bottom: 0;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .order-number {
        font-size: 20px;
        font-weight: 700;
        color: #222;
        margin-bottom: 10px;
    }

    .order-meta {
        font-size: 14px;
        color: #666;
        margin-bottom: 6px;
    }

    .order-meta strong {
        color: #222;
        font-weight: 700;
    }

    .order-actions {
        margin-top: 14px;
    }

    .order-actions .btn {
        border-radius: 999px;
        padding: 8px 18px;
    }

    .empty-orders {
        text-align: center;
        padding: 40px 20px;
        color: #777;
        font-size: 15px;
    }

    html[dir="rtl"] .orders-card,
    html[dir="rtl"] .order-item,
    html[dir="rtl"] .empty-orders {
        text-align: right;
    }

    @media (max-width: 767px) {
        .orders-card {
            padding: 20px 16px;
            border-radius: 18px;
        }

        .orders-title {
            font-size: 24px;
        }

        .order-number {
            font-size: 18px;
        }
    }
</style>

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
                        <strong>{{ __('site.status') }}:</strong> {{ $order->status }}
                    </p>

                    <p class="order-meta">
                        <strong>{{ __('site.shipping_status') }}:</strong> {{ $order->shipping_status }}
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
