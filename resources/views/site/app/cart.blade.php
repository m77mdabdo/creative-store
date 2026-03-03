@php
    use Illuminate\Support\Facades\Storage;

    $cart = session()->get('cart', []);
    $items = collect($cart);

    $cartCount = $items->sum('quantity');

    $cartTotal = $items->sum(function ($item) {
        return (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0);
    });

    $currency = $items->first()['currency'] ?? 'SAR';
@endphp

<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>

    <div class="header-cart flex-col-l p-l-65 p-r-25">

        {{-- Header --}}
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">
                Your Cart ({{ $cartCount }})
            </span>

            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="header-cart-content flex-w js-pscroll">

            <ul class="header-cart-wrapitem w-full">

                @forelse($items as $key => $item)

                    @php
                        $name  = $item['name'] ?? 'Product';
                        $qty   = (int)($item['quantity'] ?? 1);
                        $price = (float)($item['price'] ?? 0);
                        $image = $item['image'] ?? null;

                        // Fix image path safely
                        if (!$image) {
                            $imgUrl = asset('cozastore/images/item-cart-01.jpg');
                        } elseif (str_starts_with($image, 'http')) {
                            $imgUrl = $image;
                        } elseif (str_starts_with($image, '/')) {
                            $imgUrl = $image;
                        } else {
                            $imgUrl = Storage::url($image);
                        }
                    @endphp

                    <li class="header-cart-item flex-w flex-t m-b-12">

                        {{-- Image --}}
                        <div class="header-cart-item-img">
                            <img src="{{ $imgUrl }}" alt="{{ $name }}">
                        </div>

                        {{-- Info --}}
                        <div class="header-cart-item-txt p-t-8">

                            <span class="header-cart-item-name m-b-6">
                                {{ $name }}
                            </span>

                            <span class="header-cart-item-info">
                                {{ $qty }} × {{ number_format($price, 2) }} {{ $currency }}
                            </span>

                            {{-- Remove --}}
                            <div class="p-t-6">
                                <form action="{{ route('cart.remove', $key) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            style="background:none;border:none;color:#e3342f;font-size:12px;cursor:pointer;">
                                        Remove
                                    </button>
                                </form>
                            </div>

                        </div>

                    </li>

                @empty

                    <li class="p-t-30 p-b-30 text-center w-full">
                        <span class="stext-102 cl3">
                            Your cart is empty.
                        </span>
                    </li>

                @endforelse

            </ul>

            {{-- Footer --}}
            <div class="w-full">

                <div class="header-cart-total w-full p-tb-40">
                    Total: {{ number_format($cartTotal, 2) }} {{ $currency }}
                </div>

                <div class="header-cart-buttons flex-w w-full">

                    <a href="{{ url('cart/view') }}"
                       class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                        View Cart
                    </a>

                    <a href="{{ route('checkout.index') }}"
                       class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                        Check Out
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>
