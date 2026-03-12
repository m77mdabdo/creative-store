@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
<div class="container">

<h3 class="mtext-111 cl2 m-b-30">Order Details</h3>

<div class="bor10 p-4 m-b-30">

<p><strong>Order Number:</strong> {{ $order->order_number }}</p>
<p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
<p><strong>Shipping Status:</strong> {{ ucfirst($order->shipping_status) }}</p>
<p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
<p><strong>Shipping Method:</strong> {{ optional($order->shippingMethod)->name }}</p>
<p><strong>Total:</strong> {{ number_format($order->total, 2) }} SAR</p>

{{-- Cancel Order Button --}}

@if(
$order->status !== 'cancelled' &&
$order->shipping_status === 'pending' &&
$order->created_at->diffInHours(now()) < 3
)

<form action="{{ route('shipping.orders.cancel',$order->id) }}"
method="POST"
class="mt-3">

@csrf

<button class="btn btn-danger"
onclick="return confirm('Are you sure you want to cancel this order?')">

<i class="fas fa-times mr-1"></i>
Cancel Order

</button>

</form>

@endif


{{-- Cancel expired message --}}

@if(
$order->created_at->diffInHours(now()) >= 3 &&
$order->status !== 'cancelled'
)

<p class="text-danger mt-2">
Order can no longer be cancelled after 3 hours.
</p>

@endif


{{-- Download Invoice --}}

<a href="{{ route('shipping.orders.invoice',$order->id) }}"
class="btn btn-dark mt-3">

<i class="fas fa-file-pdf mr-1"></i>
Download Invoice

</a>

</div>


{{-- Address --}}

<div class="bor10 p-4 m-b-30">

<h4 class="m-b-20">Shipping Address</h4>

@if($order->addressSnapshot)

<p>{{ $order->addressSnapshot->full_name }}</p>
<p>{{ $order->addressSnapshot->phone }}</p>
<p>
{{ $order->addressSnapshot->country }}
-
{{ $order->addressSnapshot->city }}
-
{{ $order->addressSnapshot->district }}
</p>

<p>{{ $order->addressSnapshot->street }}</p>

@endif

</div>


{{-- Order Items --}}

<div class="bor10 p-4 m-b-30">

<h4 class="m-b-20">Items</h4>

@foreach($order->items as $item)

<div class="m-b-15"
style="border-bottom:1px solid #eee;padding-bottom:10px;">

<p><strong>{{ $item->product_name_snapshot }}</strong></p>

<p>Quantity: {{ $item->quantity }}</p>

<p>Price: {{ number_format($item->unit_price,2) }} SAR</p>

</div>

@endforeach

</div>


{{-- Shipment Tracking --}}

<div class="bor10 p-4">

<h4 class="m-b-20">Shipment Tracking</h4>

@if($order->shipment)

<p><strong>Carrier:</strong> {{ $order->shipment->carrier }}</p>

<p>
<strong>Tracking Number:</strong>
{{ $order->shipment->tracking_number ?? 'Not available yet' }}
</p>

<p><strong>Status:</strong> {{ $order->shipment->status }}</p>

<p>
<strong>Current Location:</strong>
{{ $order->shipment->current_location ?? '-' }}
</p>

<p>
<strong>Carrier Status:</strong>
{{ $order->shipment->carrier_status ?? '-' }}
</p>

@else

<p class="text-muted">Shipment not created yet.</p>

@endif

</div>

</div>
</section>
@endsection
