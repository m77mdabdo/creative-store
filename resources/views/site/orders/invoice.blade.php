<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #222;
            line-height: 1.7;
            margin: 0;
            padding: 0;
        }

        .invoice-wrapper {
            padding: 24px 28px;
        }

        .invoice-header {
            width: 100%;
            border-bottom: 2px solid #111;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .invoice-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-header-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .brand-left {
            width: 55%;
        }

        .brand-right {
            width: 45%;
            text-align: right;
        }

        .brand-logo {
            height: 52px;
            margin-bottom: 10px;
        }

        .brand-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 4px;
            color: #111;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        .invoice-badge {
            display: inline-block;
            background: #111;
            color: #fff;
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            margin: 0 0 6px;
        }

        .invoice-meta {
            margin-top: 16px;
        }

        .invoice-meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-meta-table td {
            border: none;
            padding: 8px 10px;
            background: #fafafa;
        }

        .invoice-meta-table tr + tr td {
            border-top: 1px solid #ececec;
        }

        .meta-label {
            font-weight: bold;
            width: 25%;
            white-space: nowrap;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 24px 0 10px;
            color: #111;
        }

        .address-box {
            border: 1px solid #e5e5e5;
            background: #fafafa;
            padding: 14px 16px;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 9px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .product-name {
            font-weight: bold;
            margin-bottom: 4px;
            color: #111;
        }

        .item-meta {
            font-size: 11px;
            color: #555;
            margin: 2px 0;
        }

        .totals {
            margin-top: 20px;
            width: 320px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals th,
        .totals td {
            padding: 9px;
        }

        .totals .grand-total th,
        .totals .grand-total td {
            background: #111;
            color: #fff;
            font-weight: bold;
        }

        .qr-barcode-wrap {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #e8e8e8;
        }

        .qr-barcode-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-barcode-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .qr-box,
        .barcode-box {
            border: 1px solid #e5e5e5;
            background: #fafafa;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
        }

        .qr-box img,
        .barcode-box img {
            max-width: 100%;
            height: auto;
        }

        .code-title {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }

        .invoice-footer {
            margin-top: 26px;
            padding-top: 14px;
            border-top: 1px solid #ececec;
            font-size: 11px;
            color: #666;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        html[dir="rtl"] body {
            text-align: right;
        }

        html[dir="rtl"] th,
        html[dir="rtl"] td,
        html[dir="rtl"] .brand-right,
        html[dir="rtl"] .text-right {
            text-align: right;
        }

        html[dir="rtl"] .totals {
            margin-left: 0;
            margin-right: auto;
        }

        html[dir="rtl"] .brand-left {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">

        <div class="invoice-header">
            <table class="invoice-header-table">
                <tr>
                    <td class="brand-left">
                        @php
                            $logoPath = public_path('cozastore/images/icons/logo-01.png');
                        @endphp

                        @if(file_exists($logoPath))
                            <img src="{{ $logoPath }}" alt="Creative Store" class="brand-logo">
                        @endif

                        <p class="brand-title">Creative Store</p>
                        <p class="brand-subtitle">{{ __('site.invoice') }}</p>
                    </td>

                    <td class="brand-right">
                        <div class="invoice-badge">{{ __('site.invoice') }}</div>
                        <h1 class="invoice-title">#{{ $order->order_number }}</h1>
                        <div>{{ $order->created_at->format('d M Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-meta">
            <table class="invoice-meta-table">
                <tr>
                    <td class="meta-label">{{ __('site.order_number') }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td class="meta-label">{{ __('site.date') }}</td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">{{ __('site.status') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td class="meta-label">{{ __('site.payment_status') }}</td>
                    <td>{{ ucfirst($order->payment_status) }}</td>
                </tr>
                <tr>
                    <td class="meta-label">{{ __('site.total') }}</td>
                    <td colspan="3">{{ number_format((float) $order->total, 2) }} {{ $order->currency ?? 'SAR' }}</td>
                </tr>
            </table>
        </div>

        @if($order->addressSnapshot)
            <div class="section-title">{{ __('site.shipping_address') }}</div>

            <div class="address-box">
                <strong>{{ $order->addressSnapshot->full_name }}</strong><br>
                {{ $order->addressSnapshot->phone }}<br>
                {{ $order->addressSnapshot->street }}<br>

                @if($order->addressSnapshot->district)
                    {{ $order->addressSnapshot->district }}<br>
                @endif

                {{ $order->addressSnapshot->city }}, {{ $order->addressSnapshot->country }}<br>

                @if($order->addressSnapshot->postal_code)
                    {{ __('site.postal_code') }}: {{ $order->addressSnapshot->postal_code }}<br>
                @endif

                @if($order->addressSnapshot->building_no)
                    {{ __('site.building_no') }}: {{ $order->addressSnapshot->building_no }}<br>
                @endif

                @if($order->addressSnapshot->apartment_no)
                    {{ __('site.apartment_no') }}: {{ $order->addressSnapshot->apartment_no }}<br>
                @endif
            </div>
        @endif

        <div class="section-title">{{ __('site.items') }}</div>

        <table>
            <tr>
                <th style="width: 45%;">{{ __('site.product') }}</th>
                <th style="width: 15%;">{{ __('site.quantity') }}</th>
                <th style="width: 20%;">{{ __('site.unit_price') }}</th>
                <th style="width: 20%;">{{ __('site.total') }}</th>
            </tr>

            @foreach($order->items as $item)
                @php
                    $variantOptions = collect($item->variant_options ?? []);

                    $displayOptions = $variantOptions->reject(function ($option) use ($item) {
                        $slug = strtolower(trim((string) ($option['slug'] ?? '')));
                        $name = strtolower(trim((string) ($option['name'] ?? '')));

                        $isColor = in_array($slug, ['color', 'colors'], true)
                            || in_array($name, ['color', 'colors', 'اللون', 'الألوان'], true);

                        $isSize = in_array($slug, ['size', 'sizes'], true)
                            || in_array($name, ['size', 'sizes', 'المقاس', 'الحجم'], true);

                        if ($isColor && !empty($item->color)) {
                            return true;
                        }

                        if ($isSize && !empty($item->size)) {
                            return true;
                        }

                        return false;
                    })->values();
                @endphp

                <tr>
                    <td>
                        <div class="product-name">{{ $item->product_name_snapshot }}</div>

                        @if($item->sku_snapshot)
                            <div class="item-meta"><strong>SKU:</strong> {{ $item->sku_snapshot }}</div>
                        @endif

                        @if($item->color)
                            <div class="item-meta"><strong>{{ __('site.color') }}:</strong> {{ $item->color }}</div>
                        @endif

                        @if($item->size)
                            <div class="item-meta"><strong>{{ __('site.modal_size') }}:</strong> {{ $item->size }}</div>
                        @endif

                        @foreach($displayOptions as $option)
                            <div class="item-meta">
                                <strong>{{ $option['name'] ?? __('site.option') }}:</strong>
                                {{ $option['value'] ?? '-' }}
                            </div>
                        @endforeach
                    </td>

                    <td>{{ (int) $item->quantity }}</td>

                    <td>
                        {{ number_format((float) $item->unit_price, 2) }}
                        {{ $order->currency ?? 'SAR' }}
                    </td>

                    <td>
                        {{ number_format((float) $item->line_total, 2) }}
                        {{ $order->currency ?? 'SAR' }}
                    </td>
                </tr>
            @endforeach
        </table>

        <table class="totals">
            <tr>
                <th>{{ __('site.subtotal') }}</th>
                <td class="text-right">
                    {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency ?? 'SAR' }}
                </td>
            </tr>

            @if((float) $order->discount > 0)
                <tr>
                    <th>{{ __('site.discount') }}</th>
                    <td class="text-right">
                        - {{ number_format((float) $order->discount, 2) }} {{ $order->currency ?? 'SAR' }}
                    </td>
                </tr>
            @endif

            <tr>
                <th>{{ __('site.shipping') }}</th>
                <td class="text-right">
                    {{ number_format((float) $order->shipping_fee, 2) }} {{ $order->currency ?? 'SAR' }}
                </td>
            </tr>

            <tr>
                <th>{{ __('site.vat') }}</th>
                <td class="text-right">
                    {{ number_format((float) $order->tax_total, 2) }} {{ $order->currency ?? 'SAR' }}
                </td>
            </tr>

            <tr class="grand-total">
                <th>{{ __('site.total') }}</th>
                <td class="text-right">
                    {{ number_format((float) $order->total, 2) }} {{ $order->currency ?? 'SAR' }}
                </td>
            </tr>
        </table>

        <div class="qr-barcode-wrap">
            <table class="qr-barcode-table">
                <tr>
                    <td style="width: 48%;">
                        <div class="qr-box">
                            <div class="code-title">QR Code</div>
                            @if(!empty($qrCodeBase64))
                                <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                            @else
                                <div>{{ $order->order_number }}</div>
                            @endif
                        </div>
                    </td>

                    <td style="width: 4%;"></td>

                    <td style="width: 48%;">
                        <div class="barcode-box">
                            <div class="code-title">Barcode</div>
                            @if(!empty($barcodeBase64))
                                <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode">
                            @else
                                <div>*{{ $order->order_number }}*</div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-footer">
            Creative Store — {{ __('site.invoice') }} #{{ $order->order_number }}
        </div>

    </div>
</body>
</html>
