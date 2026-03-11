@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <h3 class="mtext-111 cl2 m-b-30">Order Details</h3>

        <div class="bor10 p-4 m-b-30">
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Status:</strong> {{ $order->status }}</p>
            <p><strong>Shipping Status:</strong> {{ $order->shipping_status }}</p>
            <p><strong>Payment Status:</strong> {{ $order->payment_status }}</p>
            <p><strong>Shipping Method:</strong> {{ optional($order->shippingMethod)->name }}</p>
            <p><strong>Total:</strong> {{ number_format($order->total, 2) }} SAR</p>
        </div>

        <div class="bor10 p-4 m-b-30">
            <h4 class="m-b-20">Address</h4>
            @if($order->addressSnapshot)
                <p>{{ $order->addressSnapshot->full_name }}</p>
                <p>{{ $order->addressSnapshot->phone }}</p>
                <p>{{ $order->addressSnapshot->country }} - {{ $order->addressSnapshot->city }} - {{ $order->addressSnapshot->district }}</p>
                <p>{{ $order->addressSnapshot->street }}</p>
            @endif
        </div>

        <div class="bor10 p-4 m-b-30">
            <h4 class="m-b-20">Items</h4>
            @foreach($order->items as $item)
                <div class="m-b-15" style="border-bottom:1px solid #eee; padding-bottom:10px;">
                    <p><strong>{{ $item->product_name_snapshot }}</strong></p>
                    <p>Qty: {{ $item->quantity }}</p>
                    <p>Price: {{ number_format($item->unit_price, 2) }} SAR</p>
                </div>
            @endforeach
        </div>

        <div class="bor10 p-4">
            <h4 class="m-b-20">Shipment</h4>
            @if($order->shipment)
                <p><strong>Carrier:</strong> {{ $order->shipment->carrier }}</p>
                <p><strong>Tracking Number:</strong> {{ $order->shipment->tracking_number ?? 'Not available yet' }}</p>
                <p><strong>Status:</strong> {{ $order->shipment->status }}</p>
                <p><strong>Current Location:</strong> {{ $order->shipment->current_location ?? '-' }}</p>
                <p><strong>Carrier Status:</strong> {{ $order->shipment->carrier_status ?? '-' }}</p>
            @endif
        </div>
    </div>
</section>
@endsection
