@extends('dashboard.app.layout')

@section('title', 'Recovery Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Recovery Details</h1>
            <a href="{{ route('dashboard.abandoned-cart-recoveries.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="180">Recovery ID</th>
                                    <td>{{ $recovery->id }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>{{ $recovery->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $recovery->email ?? $recovery->user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Session ID</th>
                                    <td>{{ $recovery->session_id ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Channel</th>
                                    <td>{{ ucfirst($recovery->channel) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{{ ucfirst($recovery->status) }}</td>
                                </tr>
                                <tr>
                                    <th>Recovery Token</th>
                                    <td style="word-break: break-all;">{{ $recovery->recovery_token }}</td>
                                </tr>
                                <tr>
                                    <th>Cart Abandoned At</th>
                                    <td>{{ $recovery->cart_abandoned_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Sent At</th>
                                    <td>{{ $recovery->sent_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Recovered At</th>
                                    <td>{{ $recovery->recovered_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Expires At</th>
                                    <td>{{ $recovery->expires_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Items Count</th>
                                    <td>{{ $summary['cart_items_count'] }}</td>
                                </tr>
                                <tr>
                                    <th>Total Qty</th>
                                    <td>{{ $summary['cart_qty_total'] }}</td>
                                </tr>
                                <tr>
                                    <th>Cart Subtotal</th>
                                    <td>{{ number_format((float) $summary['cart_subtotal'], 2) }} SAR</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td>{{ $recovery->note ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recovered Cart Items</h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Variant SKU</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recovery->cart?->items ?? [] as $item)
                                    @php
                                        $product = $item->product;
                                        $variant = $item->variant;
                                        $translation = $product?->translations->firstWhere('locale', app()->getLocale())
                                            ?? $product?->translations->firstWhere('locale', config('app.fallback_locale'))
                                            ?? $product?->translations->first();
                                        $productName = $translation->name ?? ('Product #' . ($product->id ?? '-'));
                                        $lineTotal = ((float) $item->price_snapshot) * ((int) $item->quantity);
                                    @endphp
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            @if($product)
                                                <a href="{{ route('dashboard.products.show', $product) }}">{{ $productName }}</a>
                                            @else
                                                <span class="text-muted">Deleted Product</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->sku ?? '-' }}</td>
                                        <td>{{ $variant->sku ?? '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format((float) $item->price_snapshot, 2) }}</td>
                                        <td>{{ number_format((float) $lineTotal, 2) }}</td>
                                        <td>
                                            @if(($variant && $variant->is_active) || (!$variant && $product && $product->status === 'active'))
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No cart items found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($recovery->cart)
                        <div class="mt-2">
                            <a href="{{ route('dashboard.carts.show', $recovery->cart) }}" class="btn btn-info">
                                Open Cart
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
