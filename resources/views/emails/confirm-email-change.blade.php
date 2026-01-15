@php
    $rtl = app()->getLocale() === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('account.email_change.subject', ['store' => config('app.name')]) }}</title>
</head>
<body style="margin:0; font-family: Tahoma, Arial, sans-serif; background:#f4f5fb; padding:30px 12px;">
    <div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:16px; border:1px solid #e5e7eb; padding:28px 30px; text-align:{{ $rtl ? 'right' : 'left' }};">
        <h1 style="margin:0 0 12px; font-size:20px; color:#111827;">{{ __('account.email_change.title', ['name' => $name]) }}</h1>
        <p style="color:#374151; font-size:15px; line-height:1.8; margin:0 0 22px;">{{ __('account.email_change.body') }}</p>
        <div style="text-align:center; margin:0 0 22px;">
            <a href="{{ $confirmUrl }}" style="display:inline-block; padding:13px 28px; background:#111827; color:#ffffff; text-decoration:none; border-radius:10px; font-weight:bold;">
                {{ __('account.email_change.button') }}
            </a>
        </div>
        <p style="color:#6b7280; font-size:12.5px; line-height:1.7; margin:0;">
            {{ __('account.email_change.expiry', ['minutes' => $validMinutes]) }}<br>
            {{ __('account.email_change.ignore') }}
        </p>
    </div>
</body>
</html>
