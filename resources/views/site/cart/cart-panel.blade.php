@extends('site.app.layout')

@section('content')

<style>
    .remove-cart-btn {
        background: transparent;
        border: none;
        font-size: 22px;
        color: #e74c3c;
        cursor: pointer;
    }

    .remove-cart-btn:hover {
        color: #000;
    }

    .column-6 {
        width: 60px;
        text-align: center;
    }
</style>

@php
    use Illuminate\Support\Facades\Storage;

    $cart = session()->get('cart', []);
    $items = collect($cart);

    $subtotalValue = $subtotal ?? $items->sum(fn($item) => (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0));
    $discountValue = $discount ?? 0;
    $finalTotal = $grandTotal ?? max($subtotalValue - $discountValue, 0);
    $currency = $items->first()['currency'] ?? 'SAR';
@endphp

<section class="bg0 p-t-75 p-b-85">
    <div class="container">

        {{-- alerts --}}
        @if(session('success'))
            <div class="alert alert-success m-b-20">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger m-b-20">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger m-b-20">
                <ul class="m-b-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- عنوان + زر Clear --}}
        <div class="p-b-30 flex-w flex-sb-m">
            <h3 class="ltext-103 cl5">Shopping Cart</h3>

            @if($items->count())
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Clear all cart items?')">
                    @csrf
                    <button type="submit" class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04">
                        Clear Cart
                    </button>
                </form>
            @endif
        </div>

        @if($items->count())

            <div class="row">

                {{-- LEFT: TABLE --}}
                <div class="col-lg-8 m-lr-auto m-b-50">
                    <div class="m-l-25 m-r--38 m-lr-0-xl">

                        <div class="wrap-table-shopping-cart">
                            <table class="table-shopping-cart">
                                <tr class="table_head">
                                    <th class="column-1">Product</th>
                                    <th class="column-2"></th>
                                    <th class="column-3">Price</th>
                                    <th class="column-4">Quantity</th>
                                    <th class="column-5">Total</th>
                                    <th class="column-6"></th>
                                </tr>

                                @foreach($items as $key => $item)
                                    @php
                                        $name = $item['name'] ?? 'Product';
                                        $qty = (int) ($item['quantity'] ?? 1);
                                        $price = (float) ($item['price'] ?? 0);
                                        $image = $item['image'] ?? null;

                                        if (!$image) {
                                            $imgUrl = asset('cozastore/images/item-cart-01.jpg');
                                        } elseif (str_starts_with($image, 'http')) {
                                            $imgUrl = $image;
                                        } elseif (str_starts_with($image, '/')) {
                                            $imgUrl = $image;
                                        } else {
                                            $imgUrl = Storage::url($image);
                                        }

                                        $safeKey = md5((string) $key);
                                    @endphp

                                    <tr class="table_row">

                                        <td class="column-1">
                                            <div class="how-itemcart1">
                                                <img src="{{ $imgUrl }}" alt="{{ $name }}">
                                            </div>
                                        </td>

                                        <td class="column-2">{{ $name }}</td>

                                        <td class="column-3">{{ number_format($price, 2) }} {{ $currency }}</td>

                                        <td class="column-4">
                                            <form action="{{ route('cart.update') }}" method="POST" class="flex-w flex-m">
                                                @csrf
                                                <input type="hidden" name="key" value="{{ $key }}">

                                                <div class="wrap-num-product flex-w m-l-auto m-r-0 m-r-10">
                                                    <button type="button"
                                                        class="my-btn-down cl8 hov-btn3 trans-04 flex-c-m"
                                                        onclick="changeQty('{{ $safeKey }}', -1)">
                                                        <i class="fs-16 zmdi zmdi-minus"></i>
                                                    </button>

                                                    <input id="qty-{{ $safeKey }}"
                                                        class="mtext-104 cl3 txt-center num-product"
                                                        type="number"
                                                        name="quantity"
                                                        value="{{ $qty }}"
                                                        min="1">

                                                    <button type="button"
                                                        class="my-btn-up cl8 hov-btn3 trans-04 flex-c-m"
                                                        onclick="changeQty('{{ $safeKey }}', 1)">
                                                        <i class="fs-16 zmdi zmdi-plus"></i>
                                                    </button>
                                                </div>

                                                <button type="submit"
                                                    class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer"
                                                    style="height:40px;">
                                                    Update
                                                </button>
                                            </form>
                                        </td>

                                        <td class="column-5">{{ number_format($price * $qty, 2) }} {{ $currency }}</td>

                                        <td class="column-6">
                                            <form action="{{ route('cart.remove', ['key' => $key]) }}" method="POST"
                                                onsubmit="return confirm('Remove this item?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="remove-cart-btn"
                                                    title="Remove item">
                                                    <i class="zmdi zmdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        {{-- coupon --}}
                        <div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">

                            <div class="flex-w flex-m m-r-20 m-tb-5">
                                <form action="{{ route('cart.coupon') }}" method="POST" class="flex-w flex-m">
                                    @csrf

                                    <input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5"
                                        type="text"
                                        name="coupon"
                                        value="{{ session('cart_coupon.code') }}"
                                        placeholder="Coupon Code">

                                    <button type="submit"
                                        class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
                                        Apply coupon
                                    </button>
                                </form>

                                @if(session('cart_coupon'))
                                    <form action="{{ route('cart.coupon.remove') }}" method="POST" class="m-l-10 m-tb-5">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer">
                                            Remove coupon
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <a href="{{ route('cart.index') }}"
                                class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10">
                                Refresh Cart
                            </a>
                        </div>

                        {{-- Continue Shopping --}}
                        <div class="flex-w flex-sb-m p-t-30">
                            <a href="{{ url('/products') }}"
                                class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04">
                                Continue Shopping
                            </a>
                        </div>

                    </div>
                </div>

                {{-- RIGHT: TOTALS --}}
                <div class="col-sm-10 col-lg-4 col-xl-4 m-lr-auto m-b-50">
                    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-25 m-lr-0-xl">

                        <h4 class="mtext-109 cl2 p-b-30">Cart Totals</h4>

                        <div class="flex-w flex-t bor12 p-b-13">
                            <div class="size-208">
                                <span class="stext-110 cl2">Subtotal:</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2">{{ number_format($subtotalValue, 2) }} {{ $currency }}</span>
                            </div>
                        </div>

                        @if(session('cart_coupon'))
                            <div class="flex-w flex-t p-t-20 p-b-13">
                                <div class="size-208">
                                    <span class="stext-110 cl2">Coupon:</span>
                                </div>
                                <div class="size-209">
                                    <span class="mtext-110 cl2">{{ session('cart_coupon.code') }}</span>
                                </div>
                            </div>

                            <div class="flex-w flex-t p-b-13">
                                <div class="size-208">
                                    <span class="stext-110 cl2">Discount:</span>
                                </div>
                                <div class="size-209">
                                    <span class="mtext-110 cl2">- {{ number_format($discountValue, 2) }} {{ $currency }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="flex-w flex-t p-t-20 p-b-13">
                            <div class="size-208">
                                <span class="stext-110 cl2">Total:</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl2">{{ number_format($finalTotal, 2) }} {{ $currency }}</span>
                            </div>
                        </div>

                        <div class="p-t-27">
                            <a href="{{ route('checkout.index') }}"
                                class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                                Proceed to Checkout
                            </a>
                        </div>

                    </div>
                </div>

            </div>

        @else
            <div class="text-center p-t-100 p-b-100">
                <h4 class="mtext-105 cl2">Your cart is empty</h4>
                <p class="stext-102 cl3 p-t-10">Add some products to continue.</p>

                <a href="{{ url('/products') }}"
                    class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer m-t-20"
                    style="display:inline-flex;width:auto;">
                    Continue Shopping
                </a>
            </div>
        @endif

    </div>
</section>

<script>
    function changeQty(safeKey, delta) {
        const input = document.getElementById('qty-' + safeKey);
        if (!input) return;

        let val = parseInt(input.value || '1', 10);
        if (isNaN(val)) val = 1;

        val += delta;
        if (val < 1) val = 1;

        input.value = val;
    }
</script>

@endsection
