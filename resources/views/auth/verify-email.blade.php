<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>{{ __('site.verify_email_title') }} | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/tokens.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/site/auth-verify-email.css') }}">
</head>
<body>
    <main class="otp-card">
        <a href="{{ route('home') }}" class="otp-logo">
            <img src="{{ asset('cozastore/images/icons/creative-store-logo.png') }}" alt="{{ config('app.name') }}" width="56" height="56">
        </a>

        <h1>{{ __('site.verify_email_heading') }}</h1>
        <p class="otp-hint">{{ __('site.verify_email_hint', ['email' => $email]) }}</p>

        @if (session('error'))
            <p class="otp-alert" role="alert">{{ session('error') }}</p>
        @endif

        @if ($errors->any())
            <p class="otp-alert" role="alert">{{ $errors->first() }}</p>
        @endif

        <form id="otpForm" action="{{ route('email.verify.submit') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="otp-inputs" dir="ltr">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" name="otp[]" class="otp-input" inputmode="numeric" maxlength="1"
                           pattern="[0-9]" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                           aria-label="{{ $i + 1 }}" required @if ($i === 0) autofocus @endif>
                @endfor
            </div>

            <button type="submit" class="otp-submit">{{ __('site.verify_email_submit') }}</button>
        </form>

        <p class="otp-footnote">{{ __('site.verify_email_expires') }}</p>
        <a href="{{ route('register') }}" class="otp-link">{{ __('site.verify_email_wrong_address') }}</a>
    </main>

    <script>
        (function () {
            const inputs = Array.from(document.querySelectorAll('.otp-input'));
            const form = document.getElementById('otpForm');

            const fill = (digits, start) => {
                digits.split('').forEach((digit, offset) => {
                    if (inputs[start + offset]) inputs[start + offset].value = digit;
                });
                const next = inputs.find((input) => input.value === '');
                (next || inputs[inputs.length - 1]).focus();
                if (inputs.every((input) => /^[0-9]$/.test(input.value))) form.submit();
            };

            inputs.forEach((input, index) => {
                input.addEventListener('input', () => {
                    const digits = input.value.replace(/\D/g, '');
                    input.value = '';
                    if (digits) fill(digits, index);
                });

                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Backspace' && input.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (event) => {
                    event.preventDefault();
                    const digits = (event.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                    if (digits) fill(digits, index);
                });
            });
        })();
    </script>
</body>
</html>
