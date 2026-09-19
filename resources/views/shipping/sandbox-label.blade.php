@php $address = $order?->addressSnapshot; @endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 12px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        .box { border: 2px solid #000; padding: 8px; margin-bottom: 8px; }
        .carrier { font-size: 14px; font-weight: bold; }
        .awb { font-size: 20px; font-weight: bold; letter-spacing: 2px; text-align: center; margin: 6px 0; }
        .bars { height: 46px; text-align: center; font-family: DejaVu Sans Mono, monospace; font-size: 30px; letter-spacing: -2px; overflow: hidden; }
        .label { font-size: 8px; text-transform: uppercase; color: #444; }
        .big { font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <div class="carrier">{{ $carrier }}</div>
        <div class="label">Air waybill</div>
        <div class="bars">{{ str_repeat('|', 2) }}{{ preg_replace('/./', '| ', $trackingNumber) }}{{ str_repeat('|', 2) }}</div>
        <div class="awb">{{ $trackingNumber }}</div>
    </div>

    <div class="box">
        <div class="label">Ship to</div>
        <div class="big">{{ pdf_arabic((string) ($address?->full_name ?? '-')) }}</div>
        <div>{{ $address?->phone }}</div>
        <div>{{ pdf_arabic(collect([$address?->street, $address?->district])->filter()->implode(', ')) }}</div>
        <div class="big">{{ pdf_arabic((string) ($address?->city ?? '')) }} {{ $address?->postal_code }}</div>
    </div>

    <div class="box">
        <div class="label">From</div>
        <div>{{ config('store.legal.name_en') }}</div>
        <div>{{ config('store.legal.address_en') }}</div>
    </div>

    <div class="box">
        <div class="label">Order</div>
        <div class="big">{{ $order?->order_number }}</div>
        <div>{{ $order?->items->sum('quantity') }} item(s) &middot; {{ now()->format('Y-m-d') }}</div>
    </div>
</body>
</html>
