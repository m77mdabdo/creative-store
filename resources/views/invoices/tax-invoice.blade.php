@php
    /** Arabic text must be shaped for dompdf, which cannot join letters itself. */
    $ar = fn ($text) => pdf_arabic((string) $text);
    $amount = fn ($value) => number_format((float) $value, 2);
@endphp
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>{{ $number }}</title>
    <style>
        @page { margin: 28px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1f2329; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        .title { font-size: 17px; font-weight: bold; }
        .title-ar { font-size: 17px; font-weight: bold; text-align: right; }
        .muted { color: #6b7280; }
        .right { text-align: right; }
        .center { text-align: center; }
        .box { border: 1px solid #d9dde3; }
        .box td, .box th { border: 1px solid #d9dde3; padding: 6px 8px; vertical-align: top; }
        .box th { background: #f3f4f6; font-weight: bold; }
        .section { margin-top: 14px; }
        .label { color: #6b7280; width: 26%; }
        .totals { width: 58%; margin-left: auto; margin-top: 12px; }
        .grand td { font-size: 12.5px; font-weight: bold; background: #111827; color: #fff; }
        .qr { width: 150px; height: 150px; }
        .note { margin-top: 18px; font-size: 9px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>

<table class="head">
    <tr>
        <td style="width:38%;">
            <div class="title">Simplified Tax Invoice</div>
            <div class="muted">{{ $number }}</div>
            <div class="muted">{{ $issuedAt->format('Y-m-d H:i') }}</div>
        </td>
        <td style="width:24%;" class="center">
            <img class="qr" src="{{ $qrSvg }}" alt="ZATCA QR">
        </td>
        <td style="width:38%;" class="right">
            <div class="title-ar">{{ $ar('فاتورة ضريبية مبسطة') }}</div>
            <div class="muted">{{ $ar('رقم الفاتورة') }}: {{ $number }}</div>
            <div class="muted">{{ $ar('رقم الطلب') }}: {{ $order->order_number }}</div>
        </td>
    </tr>
</table>

<table class="box section">
    <tr>
        <th colspan="2">Seller</th>
        <th colspan="2" class="right">{{ $ar('البائع') }}</th>
    </tr>
    <tr>
        <td class="label">Name</td>
        <td>{{ $seller->name_en }}</td>
        <td class="right">{{ $ar($seller->name_ar) }}</td>
        <td class="label right">{{ $ar('الاسم') }}</td>
    </tr>
    <tr>
        <td class="label">VAT No.</td>
        <td colspan="2" class="center">{{ $seller->vat_number }}</td>
        <td class="label right">{{ $ar('الرقم الضريبي') }}</td>
    </tr>
    <tr>
        <td class="label">CR No.</td>
        <td colspan="2" class="center">{{ $seller->cr_number }}</td>
        <td class="label right">{{ $ar('السجل التجاري') }}</td>
    </tr>
    <tr>
        <td class="label">Address</td>
        <td>{{ $seller->address_en }}</td>
        <td class="right">{{ $ar($seller->address_ar) }}</td>
        <td class="label right">{{ $ar('العنوان') }}</td>
    </tr>
</table>

<table class="box section">
    <tr>
        <th colspan="2">Customer</th>
        <th colspan="2" class="right">{{ $ar('العميل') }}</th>
    </tr>
    <tr>
        <td class="label">Name</td>
        <td colspan="2" class="center">{{ $ar($buyer->name ?? '-') }}</td>
        <td class="label right">{{ $ar('الاسم') }}</td>
    </tr>
    <tr>
        <td class="label">Phone / Email</td>
        <td colspan="2" class="center">{{ $buyer->phone ?? '-' }} &nbsp; {{ $buyer->email }}</td>
        <td class="label right">{{ $ar('التواصل') }}</td>
    </tr>
    @if ($buyer->address)
        <tr>
            <td class="label">Address</td>
            <td colspan="2" class="center">{{ $ar($buyer->address) }}</td>
            <td class="label right">{{ $ar('العنوان') }}</td>
        </tr>
    @endif
</table>

<table class="box section">
    <tr>
        <th>Item {{ $ar('/ المنتج') }}</th>
        <th class="center" style="width:9%;">Qty<br>{{ $ar('الكمية') }}</th>
        <th class="right" style="width:15%;">Unit price<br>{{ $ar('سعر الوحدة') }}</th>
        <th class="right" style="width:16%;">Amount<br>{{ $ar('المبلغ') }}</th>
        <th class="right" style="width:15%;">VAT<br>{{ $ar('الضريبة') }}</th>
    </tr>
    @foreach ($lines as $line)
        <tr>
            <td>
                <strong>{{ $ar($line->name) }}</strong>
                @if ($line->options)<div class="muted">{{ $ar($line->options) }}</div>@endif
                @if ($line->sku)<div class="muted">SKU {{ $line->sku }}</div>@endif
            </td>
            <td class="center">{{ $line->quantity }}</td>
            <td class="right">{{ $amount($line->unit_price) }}</td>
            <td class="right">{{ $amount($line->net) }}</td>
            <td class="right">{{ $amount($line->vat) }}</td>
        </tr>
    @endforeach
</table>

<table class="box totals">
    <tr>
        <td>Subtotal</td>
        <td class="right">{{ $amount($subtotal) }} {{ $currency }}</td>
        <td class="right">{{ $ar('المجموع') }}</td>
    </tr>
    @if ($discount > 0)
        <tr>
            <td>Discount</td>
            <td class="right">- {{ $amount($discount) }} {{ $currency }}</td>
            <td class="right">{{ $ar('الخصم') }}</td>
        </tr>
    @endif
    @if ($shipping > 0)
        <tr>
            <td>Shipping</td>
            <td class="right">{{ $amount($shipping) }} {{ $currency }}</td>
            <td class="right">{{ $ar('الشحن') }}</td>
        </tr>
    @endif
    <tr>
        <td>Total excl. VAT</td>
        <td class="right">{{ $amount($taxable) }} {{ $currency }}</td>
        <td class="right">{{ $ar('الإجمالي غير شامل الضريبة') }}</td>
    </tr>
    <tr>
        <td>VAT {{ rtrim(rtrim(number_format($vatRate * 100, 2), '0'), '.') }}%</td>
        <td class="right">{{ $amount($vat) }} {{ $currency }}</td>
        <td class="right">{{ $ar('ضريبة القيمة المضافة') }}</td>
    </tr>
    <tr class="grand">
        <td>Total incl. VAT</td>
        <td class="right">{{ $amount($total) }} {{ $currency }}</td>
        <td class="right">{{ $ar('الإجمالي شامل الضريبة') }}</td>
    </tr>
</table>

<div class="note">
    {{ $ar('هذه فاتورة ضريبية مبسطة صادرة إلكترونيًا') }} &middot; This is an electronically issued simplified tax invoice.
</div>

</body>
</html>
