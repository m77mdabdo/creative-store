<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #222;
            line-height: 1.6;
        }

        h1 {
            margin-bottom: 20px;
        }

        h3 {
            margin-top: 25px;
            margin-bottom: 12px;
        }

        .meta p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td, th {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .product-name {
            font-weight: bold;
            margin-bottom: 4px;
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
        }

        .totals td, .totals th {
            padding: 8px;
        }

        .no-border td {
            border: none;
            padding: 4px 0;
        }
    </style>
</head>
<body>

    <h1>{{ __('Invoice') }}</h1>

    <div class="meta">
        <p><strong>{{ __('Order:') }}</strong> {{ $order->order_number }}</p>
        <p><strong>{{ __('Date:') }}</strong> {{ $order->created_at?->format('d M Y') }}</p>
        <p><strong>{{ __('Status:') }}</strong> {{ status_label($order->status) }}</p>
        <p><strong>{{ __('Payment Status:') }}</strong> {{ status_label($order->payment_status) }}</p>
        <p><strong>{{ __('Shipping Method:') }}</strong> {{ $order->shippingMethod?->label ?? '-' }}</p>
        <p><strong>{{ __('Total:') }}</strong> {{ number_format((float) $order->total, 2) }} {{ $order->currency ?? __('SAR') }}</p>
    </div>

    @if($order->addressSnapshot)
        <h3>{{ __('Shipping Address') }}</h3>
        <table class="no-border" style="width:100%;">
            <tr>
                <td>
                    <strong>{{ $order->addressSnapshot->full_name }}</strong><br>
                    {{ $order->addressSnapshot->phone }}<br>
                    {{ $order->addressSnapshot->street }}<br>

                    @if($order->addressSnapshot->district)
                        {{ $order->addressSnapshot->district }}<br>
                    @endif

                    {{ $order->addressSnapshot->city }}, {{ $order->addressSnapshot->country }}<br>

                    @if($order->addressSnapshot->postal_code)
                        Postal Code: {{ $order->addressSnapshot->postal_code }}<br>
                    @endif

                    @if($order->addressSnapshot->building_no)
                        Building No: {{ $order->addressSnapshot->building_no }}<br>
                    @endif

                    @if($order->addressSnapshot->apartment_no)
                        Apartment No: {{ $order->addressSnapshot->apartment_no }}<br>
                    @endif
                </td>
            </tr>
        </table>
    @endif

    <h3>{{ __('Items') }}</h3>

    <table>
        <tr>
            <th style="width: 45%;">{{ __('Product') }}</th>
            <th style="width: 15%;">{{ __('Qty') }}</th>
            <th style="width: 20%;">{{ __('Unit Price') }}</th>
            <th style="width: 20%;">{{ __('Line Total') }}</th>
        </tr>

        @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="product-name">{{ $item->product_name_snapshot }}</div>

                    @if($item->sku_snapshot)
                        <div class="item-meta"><strong>{{ __('SKU:') }}</strong> {{ $item->sku_snapshot }}</div>
                    @endif

                    @if($item->color)
                        <div class="item-meta"><strong>{{ __('Color:') }}</strong> {{ $item->color }}</div>
                    @endif

                    @if($item->size)
                        <div class="item-meta"><strong>{{ __('Size:') }}</strong> {{ $item->size }}</div>
                    @endif

                    @if(!empty($item->variant_options))
                        @foreach($item->variant_options as $option)
                            <div class="item-meta">
                                <strong>{{ $option['name'] ?? __('Option') }}:</strong>
                                {{ $option['value'] ?? '-' }}
                            </div>
                        @endforeach
                    @endif
                </td>

                <td>{{ (int) $item->quantity }}</td>

                <td>
                    {{ number_format((float) $item->unit_price, 2) }}
                    {{ $order->currency ?? __('SAR') }}
                </td>

                <td>
                    {{ number_format((float) $item->line_total, 2) }}
                    {{ $order->currency ?? __('SAR') }}
                </td>
            </tr>
        @endforeach
    </table>

    <table class="totals">
        <tr>
            <th>{{ __('Subtotal') }}</th>
            <td class="text-right">
                {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency ?? __('SAR') }}
            </td>
        </tr>

        @if((float) $order->discount > 0)
            <tr>
                <th>{{ __('Discount') }}</th>
                <td class="text-right">
                    - {{ number_format((float) $order->discount, 2) }} {{ $order->currency ?? __('SAR') }}
                </td>
            </tr>
        @endif

        <tr>
            <th>{{ __('Shipping') }}</th>
            <td class="text-right">
                {{ number_format((float) $order->shipping_fee, 2) }} {{ $order->currency ?? __('SAR') }}
            </td>
        </tr>

        <tr>
            <th>{{ __('VAT') }}</th>
            <td class="text-right">
                {{ number_format((float) $order->tax_total, 2) }} {{ $order->currency ?? __('SAR') }}
            </td>
        </tr>

        <tr>
            <th>{{ __('Total') }}</th>
            <td class="text-right">
                <strong>{{ number_format((float) $order->total, 2) }} {{ $order->currency ?? __('SAR') }}</strong>
            </td>
        </tr>
    </table>

</body>
</html>
