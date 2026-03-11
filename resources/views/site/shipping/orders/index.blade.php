@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <h3 class="mtext-111 cl2 m-b-30">My Orders</h3>

        @if(session('success'))
            <div class="alert alert-success m-b-20">{{ session('success') }}</div>
        @endif

        <div class="bor10 p-4">
            @forelse($orders as $order)
                <div class="p-b-20 m-b-20" style="border-bottom:1px solid #eee;">
                    <h5>Order #{{ $order->order_number }}</h5>
                    <p>Status: {{ $order->status }}</p>
                    <p>Shipping Status: {{ $order->shipping_status }}</p>
                    <p>Total: {{ number_format($order->total, 2) }} SAR</p>
                    <a href="{{ route('shipping.orders.show', $order) }}" class="btn btn-sm btn-dark">View Order</a>
                </div>
            @empty
                <p>No orders found.</p>
            @endforelse

            {{ $orders->links() }}
        </div>
    </div>
</section>
@endsection


