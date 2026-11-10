@php
    $rtl = app()->getLocale() === 'ar';
    $align = $rtl ? 'right' : 'left';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('notifications.cart_email.subject_' . ($tone ?? 'first')) }}</title>
</head>
<body style="font-family: Tahoma, Arial, sans-serif; background:#f8f9fa; padding:30px; direction:{{ $rtl ? 'rtl' : 'ltr' }};">
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:12px; padding:30px; border:1px solid #e5e7eb; text-align:{{ $align }};">
        <h2 style="margin-top:0; color:#111827;">{{ __('notifications.cart_email.title') }}</h2>

        <p style="color:#4b5563; font-size:15px;">
            {{ $user ? __('notifications.cart_email.greeting', ['name' => $user->name]) : __('notifications.cart_email.greeting_guest') }}
        </p>

        <p style="color:#4b5563; font-size:15px; line-height:1.7;">
            {{ __(($tone ?? 'first') === 'first' ? 'notifications.cart_email.body' : 'notifications.cart_email.body_' . $tone) }}
        </p>

        <table width="100%" cellspacing="0" cellpadding="10" style="border-collapse: collapse; margin-top:20px;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th align="{{ $align }}">{{ __('notifications.cart_email.product') }}</th>
                    <th align="center">{{ __('notifications.cart_email.quantity') }}</th>
                    <th align="{{ $rtl ? 'left' : 'right' }}">{{ __('notifications.cart_email.price') }}</th>
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

                        $productName = $translation->name ?? $product->slug;
                    @endphp
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td align="{{ $align }}">{{ $productName }}</td>
                        <td align="center">{{ $item->quantity }}</td>
                        <td align="{{ $rtl ? 'left' : 'right' }}" style="white-space:nowrap;">{{ money((float) $item->price_snapshot, $product->currency ?? 'SAR') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:30px; text-align:center;">
            <a href="{{ $recoverUrl }}"
               style="display:inline-block; padding:14px 28px; background:#111827; color:#ffffff; text-decoration:none; border-radius:8px; font-weight:bold;">
                {{ __('notifications.cart_email.button') }}
            </a>
        </div>

        <p style="margin-top:25px; color:#6b7280; font-size:13px;">
            {{ __('notifications.cart_email.expiry') }}
        </p>
    </div>
</body>
</html>
