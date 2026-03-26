@extends('dashboard.app.layout')

@section('title', 'Cart Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Cart Details</h1>
            <a href="{{ route('dashboard.carts.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="180">Cart ID</th>
                                    <td>{{ $cart->id }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ $cart->user_id ? 'User Cart' : 'Guest Cart' }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>{{ $cart->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $cart->user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $cart->user->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Session ID</th>
                                    <td>{{ $cart->session_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Items Count</th>
                                    <td>{{ $summary['items_count'] }}</td>
                                </tr>
                                <tr>
                                    <th>Total Qty</th>
                                    <td>{{ $summary['quantity_total'] }}</td>
                                </tr>
                                <tr>
                                    <th>Subtotal</th>
                                    <td>{{ number_format((float) $summary['subtotal'], 2) }} SAR</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($summary['is_abandoned'])
                                            <span class="badge badge-warning">Abandoned</span>
                                        @elseif($summary['items_count'] > 0)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-light">Empty</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>User Orders</th>
                                    <td>
                                        @if($summary['has_user_orders'])
                                            <span class="badge badge-success">{{ $summary['orders_count'] }} Orders</span>
                                        @else
                                            <span class="badge badge-secondary">No Orders</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $cart->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $cart->updated_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Cart Items</h3>
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
                                    <th>Tax</th>
                                    <th>Line Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($cart->items as $item)
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
                                        <td>{{ number_format((float) $item->tax_rate_snapshot, 2) }}%</td>
                                        <td>{{ number_format((float) $lineTotal, 2) }}</td>
                                        <td>
                                            @if(($variant && $variant->is_active) || (!$variant && $product && $product->status === 'active'))
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('dashboard.carts.items.destroy', [$cart, $item]) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this cart item?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">No items in this cart.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($cart->user && $cart->user->orders()->exists())
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent User Orders</h3>
                            </div>
                            <div class="card-body">
                                @foreach($cart->user->orders()->latest()->take(5)->get() as $order)
                                    <div class="border rounded p-2 mb-2">
                                        <strong>#{{ $order->order_number }}</strong>
                                        <span class="ml-2 badge badge-info">{{ $order->status }}</span>
                                        <span class="ml-2">{{ number_format((float) $order->total, 2) }} {{ $order->currency }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
</div>
@endsection

