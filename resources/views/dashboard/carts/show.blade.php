@extends('dashboard.app.layout')

@section('title', 'Cart Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Cart Details') }}</h1>
            <a href="{{ route('dashboard.carts.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
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
                                    <th width="180">{{ __('Cart ID') }}</th>
                                    <td>{{ $cart->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type') }}</th>
                                    <td>{{ $cart->user_id ? __('User Cart') : __('Guest Cart') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('User') }}</th>
                                    <td>{{ $cart->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{ $cart->user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <td>{{ $cart->user->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Session ID') }}</th>
                                    <td>{{ $cart->session_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Items Count') }}</th>
                                    <td>{{ $summary['items_count'] }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Total Qty') }}</th>
                                    <td>{{ $summary['quantity_total'] }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Subtotal') }}</th>
                                    <td>{{ number_format((float) $summary['subtotal'], 2) }} SAR</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        @if($summary['is_abandoned'])
                                            <span class="badge badge-warning">{{ __('Abandoned') }}</span>
                                        @elseif($summary['items_count'] > 0)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-light">{{ __('Empty') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('User Orders') }}</th>
                                    <td>
                                        @if($summary['has_user_orders'])
                                            <span class="badge badge-success">{{ $summary['orders_count'] }} Orders</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('No Orders') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created At') }}</th>
                                    <td>{{ $cart->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Updated At') }}</th>
                                    <td>{{ $cart->updated_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Cart Items') }}</h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('SKU') }}</th>
                                    <th>{{ __('Variant SKU') }}</th>
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Unit Price') }}</th>
                                    <th>{{ __('Tax') }}</th>
                                    <th>{{ __('Line Total') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
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
                                                <span class="text-muted">{{ __('Deleted Product') }}</span>
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
                                                <span class="badge badge-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('dashboard.carts.items.destroy', [$cart, $item]) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm(@js(__('Delete this cart item?')))">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">{{ __('No items in this cart.') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($cart->user && $cart->user->orders()->exists())
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Recent User Orders') }}</h3>
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

