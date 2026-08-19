<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete your order</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8f9fa; padding:30px;">
    <div style="max-width:700px; margin:0 auto; background:#ffffff; border-radius:10px; padding:30px; border:1px solid #e5e7eb;">
        <h2 style="margin-top:0; color:#111827;">You left items in your cart</h2>

        <p style="color:#4b5563; font-size:15px;">
            @if($user)
                Hi {{ $user->name }},
            @else
                Hello,
            @endif
        </p>
        

        <p style="color:#4b5563; font-size:15px;">
            You added some products to your cart but didn’t complete the checkout.
            Your selected items are still waiting for you.
        </p>

        <table width="100%" cellspacing="0" cellpadding="10" style="border-collapse: collapse; margin-top:20px;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th align="left">Product</th>
                    <th align="left">Qty</th>
                    <th align="left">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart->items as $item)
                    @php
                        $product = $item->product;
                        if (!$product) continue;

                        $translation = $product->translations->firstWhere('locale', app()->getLocale())
                            ?? $product->translations->firstWhere('locale', config('app.fallback_locale'))
                            ?? $product->translations->first();

                        $productName = $translation->name ?? ('Product #' . $product->id);
                    @endphp
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td>{{ $productName }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ money((float) $item->price_snapshot, $product->currency ?? 'SAR') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:30px; text-align:center;">
            <a href="{{ $recoverUrl }}"
               style="display:inline-block; padding:14px 28px; background:#111827; color:#ffffff; text-decoration:none; border-radius:8px;">
                Return to your cart
            </a>
        </div>

        <p style="margin-top:25px; color:#6b7280; font-size:13px;">
            This recovery link may expire after a limited time.
        </p>
    </div>
</body>
</html>

