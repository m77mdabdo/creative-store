@php
    $rtl = app()->getLocale() === 'ar';
    $store = config('app.name');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('site.contact_reply_subject') }}</title>
</head>
{{-- Static text only: the visitor's message is not repeated, so the form
     cannot be used to send arbitrary text to someone else's inbox. --}}
<body style="font-family: Tahoma, Arial, sans-serif; line-height: 1.8; color: #333; text-align: {{ $rtl ? 'right' : 'left' }};">
    <h2>{{ __('site.contact_reply_greeting', ['name' => \Illuminate\Support\Str::limit($contactMessage->name, 60)]) }}</h2>

    <p>{{ __('site.contact_reply_body', ['store' => $store]) }}</p>

    <p style="color:#6b7280; font-size:13px;">{{ __('site.contact_reply_note') }}</p>

    <p>{{ __('site.contact_reply_signoff', ['store' => $store]) }}</p>
</body>
</html>
