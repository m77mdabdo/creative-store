@extends('dashboard.app.layout')

@section('title', 'Wishlist Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Wishlist Details') }}</h1>
            <div>
                <a href="{{ route('dashboard.wishlists.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>

                <form action="{{ route('dashboard.wishlists.move-all-to-cart', $wishlist) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-success">{{ __('Move All To Cart') }}</button>
                </form>
            </div>
        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="220">{{ __('Wishlist ID') }}</th>
                            <td>{{ $wishlist->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('User') }}</th>
                            <td>{{ $wishlist->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td>{{ $wishlist->user->email ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>{{ __('Phone') }}</th>
                            <td>{{ $wishlist->user->phone ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>{{ __('Items Count') }}</th>
                            <td>{{ $wishlist->items->count() }}</td>
                        </tr>

                        <tr>
                            <th>{{ __('Created At') }}</th>
                            <td>{{ $wishlist->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Updated At') }}</th>
                            <td>{{ $wishlist->updated_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>



            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Wishlist Items') }}</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('SKU') }}</th>
                            <th>{{ __('Variant SKU') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th>{{ __('Added At') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($wishlist->items as $item)
                            @php
                                $product = $item->product;
                                $variant = $item->variant;
                                $translation = $product?->translations->firstWhere('locale', app()->getLocale())
                                    ?? $product?->translations->firstWhere('locale', config('app.fallback_locale'))
                                    ?? $product?->translations->first();
                                $productName = $translation->name ?? ('Product #' . ($product->id ?? '-'));
                                $price = $variant?->price ?? $product?->base_price ?? 0;
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
                                <td>{{ number_format((float)$price, 2) }} {{ $product->currency ?? __('SAR') }}</td>
                                <td>
                                    @if(($variant && $variant->is_active) || (!$variant && $product && $product->status === 'active'))
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $variant->stock ?? '-' }}</td>
                                <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form action="{{ route('dashboard.wishlists.items.move-to-cart', [$wishlist, $item]) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">{{ __('Move To Cart') }}</button>
                                    </form>

                                    <form action="{{ route('dashboard.wishlists.items.destroy', [$wishlist, $item]) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm(@js(__('Delete this item?')))">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">{{ __('No items in this wishlist.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection








