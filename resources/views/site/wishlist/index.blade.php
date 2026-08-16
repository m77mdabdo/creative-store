@extends('site.app.layout')

@section('title', __('site.wishlist'))

@section('content')

<!-- Title page -->
<section class="bg-img1 txt-center p-lr-15 p-tb-92"
    style="background-image: url('{{ asset('cozastore/images/bg-01.jpg') }}');">
    <h2 class="ltext-105 cl0 txt-center">
        {{ __('site.wishlist') }}
    </h2>
</section>

<!-- Wishlist -->
<section class="bg0 p-t-75 p-b-85">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success m-b-25">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-b-30">
            <h3 class="mtext-111 cl2">
                {{ __('site.my_wishlist') }}
            </h3>
            <p class="stext-115 cl6 p-t-8">
                {{ __('site.wishlist_desc') }}
            </p>
        </div>

        @if($wishlist->items->count())
            <div class="wrap-table-shopping-cart bor10">
                <table class="table-shopping-cart">
                    <tr class="table_head">
                        <th class="column-1">{{ __('site.product') }}</th>
                        <th class="column-2"></th>
                        <th class="column-3">{{ __('site.price') }}</th>
                        <th class="column-5"></th>
                    </tr>

                    @foreach($wishlist->items as $item)
                        @php
                            $product = $item->product;
                            $variant = $item->variant;
                        @endphp

                        @if(!$product)
                            @continue
                        @endif

                        @php
                            $translation = $product->translations->firstWhere('locale', app()->getLocale())
                                ?? $product->translations->firstWhere('locale', config('app.fallback_locale'))
                                ?? $product->translations->first();

                            $productName = $translation->name ?? $product->name ?? ('Product #' . $product->id);

                            $image = ($variant && !empty($variant->image))
                                ? media_url($variant->image)
                                : (($product?->primaryImage?->path)
                                    ? media_url($product->primaryImage->path)
                                    : asset('cozastore/images/product-01.jpg'));

                            $price = $variant?->price ?? $product?->base_price ?? 0;
                            $currency = $product?->currency ?? 'SAR';
                            $productUrl = url('/products/' . $product->slug);
                        @endphp

                        <tr class="table_row">
                            <td class="column-1">
                                <div class="how-itemcart1">
                                    <img src="{{ $image }}" alt="{{ $productName }}" loading="lazy" decoding="async">
                                </div>
                            </td>

                            <td class="column-2">
                                <a href="{{ $productUrl }}"
                                   class="stext-104 cl4 hov-cl1 trans-04">
                                    {{ $productName }}
                                </a>
                            </td>

                            <td class="column-3">
                                {{ money((float) $price, $currency) }}
                            </td>

                            <td class="column-5">
                                <div class="flex-w">
                                    <a href="{{ $productUrl }}"
                                       class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-10 m-b-10">
                                        {{ __('site.view') }}
                                    </a>

                                    <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="m-b-10">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="flex-c-m stext-101 cl0 size-107 bg1 bor1 hov-btn2 p-lr-15 trans-04">
                                            {{ __('site.remove') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <div class="bor10 p-lr-40 p-tb-55 text-center">
                <h4 class="mtext-109 cl2 p-b-15">
                    {{ __('site.empty_wishlist_title') }}
                </h4>

                <p class="stext-115 cl6 p-b-25">
                    {{ __('site.empty_wishlist_desc') }}
                </p>

                <a href="{{ url('/products') }}"
                   class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 m-lr-auto">
                    {{ __('site.continue_shopping') }}
                </a>
            </div>
        @endif

    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ versioned_asset('css/site/site-wishlist-index.css') }}">
@endpush
