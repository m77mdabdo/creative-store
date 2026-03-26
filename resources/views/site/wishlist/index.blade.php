@extends('site.app.layout')

@section('content')

    <!-- Title page -->
    <section class="bg-img1 txt-center p-lr-15 p-tb-92"
        style="background-image: url('{{ asset('cozastore/images/bg-01.jpg') }}');">
        <h2 class="ltext-105 cl0 txt-center">
            Wishlist
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
                    My Wishlist
                </h3>
                <p class="stext-115 cl6 p-t-8">
                    Save your favorite items and come back to them anytime.
                </p>
            </div>

            @if($wishlist->items->count())
                <div class="wrap-table-shopping-cart bor10">
                    <table class="table-shopping-cart">
                        <tr class="table_head">
                            <th class="column-1">Product</th>
                            <th class="column-2"></th>
                            <th class="column-3">Price</th>
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
                                    ? asset('storage/' . $variant->image)
                                    : (($product?->primaryImage?->path)
                                        ? \Illuminate\Support\Facades\Storage::url($product->primaryImage->path)
                                        : asset('cozastore/images/product-01.jpg'));

                                $price = $variant?->price ?? $product?->base_price ?? 0;
                                $currency = $product?->currency ?? 'SAR';
                                $productUrl = url('/products/' . $product->slug);
                            @endphp

                            <tr class="table_row">
                                <td class="column-1">
                                    <div class="how-itemcart1">
                                        <img src="{{ $image }}" alt="{{ $productName }}">
                                    </div>
                                </td>

                                <td class="column-2">
                                    <a href="{{ $productUrl }}"
                                       class="stext-104 cl4 hov-cl1 trans-04">
                                        {{ $productName }}
                                    </a>
                                </td>

                                <td class="column-3">
                                    {{ number_format((float) $price, 2) }} {{ $currency }}
                                </td>

                                <td class="column-5">
                                    <div class="flex-w">
                                        <a href="{{ $productUrl }}"
                                           class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-10 m-b-10">
                                            View
                                        </a>

                                        <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="m-b-10">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="flex-c-m stext-101 cl0 size-107 bg1 bor1 hov-btn2 p-lr-15 trans-04">
                                                Remove
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
                        Your wishlist is empty
                    </h4>

                    <p class="stext-115 cl6 p-b-25">
                        You haven’t added any products to your wishlist yet.
                    </p>

                    <a href="{{ url('/products') }}"
                       class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 m-lr-auto">
                        Continue Shopping
                    </a>
                </div>
            @endif

        </div>
    </section>

@endsection

<style>
    .table-shopping-cart .column-5 .flex-w {
        align-items: center;
        gap: 10px;
    }

    .table-shopping-cart .column-4 {
        min-width: 140px;
    }

    .table-shopping-cart .column-5 {
        min-width: 220px;
    }
</style>
