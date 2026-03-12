<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">

<style>
body{font-family: DejaVu Sans}
h1{margin-bottom:20px}
table{width:100%;border-collapse:collapse}
td,th{border:1px solid #ddd;padding:8px}
</style>

</head>

<body>

<h1>Invoice</h1>

<p><strong>Order:</strong> {{ $order->order_number }}</p>

<p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>

<p><strong>Total:</strong> {{ number_format($order->total,2) }} SAR</p>

<h3>Items</h3>

<table>

<tr>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
</tr>

@foreach($order->items as $item)

<tr>

<td>{{ $item->product_name_snapshot }}</td>

<td>{{ $item->quantity }}</td>

<td>{{ number_format($item->unit_price,2) }} SAR</td>

</tr>

@endforeach

</table>

</body>
</html>
