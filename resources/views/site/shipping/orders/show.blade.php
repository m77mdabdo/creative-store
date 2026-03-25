@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">

        <div class="p-b-30">
            <h3 class="mtext-111 cl2 m-b-10">Order Details</h3>
            <p class="stext-115 cl6">
                Review your order information, shipping address, items, and shipment tracking.
            </p>
        </div>

        {{-- Order Summary --}}
        <div class="bor10 p-4 m-b-30">
            <h4 class="m-b-20">Order Summary</h4>

            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Shipping Status:</strong> {{ ucfirst($order->shipping_status) }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            <p><strong>Shipping Method:</strong> {{ optional($order->shippingMethod)->name ?? '-' }}</p>
            <p><strong>Placed At:</strong> {{ optional($order->created_at)->format('d M Y - h:i A') }}</p>
            <p><strong>Total:</strong> {{ number_format((float) $order->total, 2) }} {{ $order->currency ?? 'SAR' }}</p>

            @if(
                $order->status !== 'cancelled' &&
                $order->shipping_status === 'pending' &&
                $order->created_at->diffInHours(now()) < 3
            )
                <form action="{{ route('shipping.orders.cancel', $order->id) }}"
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

            @if(
                $order->created_at->diffInHours(now()) >= 3 &&
                $order->status !== 'cancelled'
            )
                <p class="text-danger mt-2">
                    Order can no longer be cancelled after 3 hours.
                </p>
            @endif

            <a href="{{ route('shipping.orders.invoice', $order->id) }}"
               class="btn btn-dark mt-3">
                <i class="fas fa-file-pdf mr-1"></i>
                Download Invoice
            </a>
        </div>

        {{-- Shipping Address --}}
        <div class="bor10 p-4 m-b-30">
            <h4 class="m-b-20">Shipping Address</h4>

            @if($order->addressSnapshot)
                <p><strong>{{ $order->addressSnapshot->full_name }}</strong></p>
                <p>{{ $order->addressSnapshot->phone }}</p>

                <p>
                    {{ $order->addressSnapshot->country }}
                    -
                    {{ $order->addressSnapshot->city }}
                    @if($order->addressSnapshot->district)
                        - {{ $order->addressSnapshot->district }}
                    @endif
                </p>

                <p>{{ $order->addressSnapshot->street }}</p>

                @if($order->addressSnapshot->building_no)
                    <p>Building No: {{ $order->addressSnapshot->building_no }}</p>
                @endif

                @if($order->addressSnapshot->apartment_no)
                    <p>Apartment No: {{ $order->addressSnapshot->apartment_no }}</p>
                @endif

                @if($order->addressSnapshot->postal_code)
                    <p>Postal Code: {{ $order->addressSnapshot->postal_code }}</p>
                @endif

                @if($order->addressSnapshot->notes)
                    <p>Notes: {{ $order->addressSnapshot->notes }}</p>
                @endif
            @else
                <p class="text-muted">No shipping address snapshot found.</p>
            @endif
        </div>

        {{-- Order Items --}}
        <div class="bor10 p-4 m-b-30">
            <h4 class="m-b-20">Items</h4>

            @forelse($order->items as $item)
                @php
                    $variantOptions = collect($item->variant_options ?? []);

                    $displayOptions = $variantOptions->reject(function ($option) use ($item) {
                        $slug = strtolower(trim((string) ($option['slug'] ?? '')));
                        $name = strtolower(trim((string) ($option['name'] ?? '')));

                        $isColor = in_array($slug, ['color', 'colors'], true)
                            || in_array($name, ['color', 'colors', 'اللون', 'الألوان'], true);

                        $isSize = in_array($slug, ['size', 'sizes'], true)
                            || in_array($name, ['size', 'sizes', 'المقاس', 'الحجم'], true);

                        if ($isColor && !empty($item->color)) {
                            return true;
                        }

                        if ($isSize && !empty($item->size)) {
                            return true;
                        }

                        return false;
                    })->values();
                @endphp

                <div class="m-b-20 p-b-15" style="border-bottom:1px solid #eee;">
                    <p class="m-b-5">
                        <strong>{{ $item->product_name_snapshot }}</strong>
                    </p>

                    @if($item->sku_snapshot)
                        <p class="m-b-5">SKU: {{ $item->sku_snapshot }}</p>
                    @endif

                    @if($item->color)
                        <p class="m-b-5">Color: {{ $item->color }}</p>
                    @endif

                    @if($item->size)
                        <p class="m-b-5">Size: {{ $item->size }}</p>
                    @endif

                    @if($displayOptions->count())
                        @foreach($displayOptions as $option)
                            <p class="m-b-5">
                                {{ $option['name'] ?? 'Option' }}:
                                {{ $option['value'] ?? '-' }}
                            </p>
                        @endforeach
                    @endif

                    <p class="m-b-5">Quantity: {{ (int) $item->quantity }}</p>
                    <p class="m-b-5">Unit Price: {{ number_format((float) $item->unit_price, 2) }} {{ $order->currency ?? 'SAR' }}</p>
                    <p class="m-b-0">
                        <strong>Line Total: {{ number_format((float) $item->line_total, 2) }} {{ $order->currency ?? 'SAR' }}</strong>
                    </p>
                </div>
            @empty
                <p class="text-muted">No items found for this order.</p>
            @endforelse
        </div>

        {{-- Totals --}}
        <div class="bor10 p-4 m-b-30">
            <h4 class="m-b-20">Order Totals</h4>

            <p><strong>Subtotal:</strong> {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency ?? 'SAR' }}</p>

            @if((float) $order->discount > 0)
                <p><strong>Discount:</strong> - {{ number_format((float) $order->discount, 2) }} {{ $order->currency ?? 'SAR' }}</p>
            @endif

            <p><strong>Shipping Fee:</strong> {{ number_format((float) $order->shipping_fee, 2) }} {{ $order->currency ?? 'SAR' }}</p>
            <p><strong>VAT:</strong> {{ number_format((float) $order->tax_total, 2) }} {{ $order->currency ?? 'SAR' }}</p>
            <p class="m-b-0">
                <strong>Total:</strong> {{ number_format((float) $order->total, 2) }} {{ $order->currency ?? 'SAR' }}
            </p>
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

                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->shipment->status ?? '-')) }}</p>

                <p>
                    <strong>Current Location:</strong>
                    {{ $order->shipment->current_location ?? '-' }}
                </p>

                <p>
                    <strong>Carrier Status:</strong>
                    {{ $order->shipment->carrier_status ?? '-' }}
                </p>

                @if($order->shipment->tracking_number)
                    <a href="{{ route('tracking.search-form', ['tracking_number' => $order->shipment->tracking_number]) }}"
                       class="btn btn-primary mt-3">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        Track Full Shipment
                    </a>
                @endif
            @else
                <p class="text-muted">Shipment not created yet.</p>
            @endif
        </div>

    </div>
</section>
@endsection
