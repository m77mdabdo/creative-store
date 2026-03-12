@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="bor10 p-lr-40 p-t-40 p-b-40 text-center">
            <h3 class="mtext-111 cl2 p-b-20">Payment Successful</h3>

            @if(session('success'))
                <div class="alert alert-success m-b-20">{{ session('success') }}</div>
            @endif

            <p class="stext-115 cl6 p-b-10">
                Thank you for your order.
            </p>

            <p class="stext-115 cl6 p-b-10">
                Order Number: <strong>{{ $order->order_number }}</strong>
            </p>

            <p class="stext-115 cl6 p-b-10">
                Payment Status: <strong>{{ $order->payment_status }}</strong>
            </p>

            <p class="stext-115 cl6 p-b-30">
                Total: <strong>{{ number_format($order->total, 2) }} {{ $order->currency }}</strong>
            </p>

            <a href="{{ url('/') }}"
               class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer mx-auto">
                Continue Shopping
            </a>
        </div>
    </div>
</section>
@endsection
