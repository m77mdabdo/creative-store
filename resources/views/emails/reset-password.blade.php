<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.reset_mail_title') }}</title>
</head>
<body style="background-color:#1f2937;color:#e5e7eb;font-family:Arial,sans-serif;padding:20px;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0"
           style="max-width:600px;background-color:#111827;padding:20px;border-radius:8px;">
        <tr>
            <td align="center" style="padding-bottom:20px;">
                <h2 style="color:#10b981;font-size:24px;margin:0;">{{ __('site.reset_mail_title') }}</h2>
            </td>
        </tr>

        <tr>
            <td style="padding:20px;color:#d1d5db;">
                <p>{{ __('site.reset_mail_hello') }}</p>
                <p>{{ __('site.reset_mail_body') }}</p>

                <p style="text-align:center;margin:20px 0;">
                    <a href="{{ $resetUrl }}"
                       style="background-color:#3b82f6;color:#ffffff;padding:10px 20px;text-decoration:none;border-radius:4px;font-weight:bold;">
                        {{ __('site.reset_mail_button') }}
                    </a>
                </p>

                <p>{{ __('site.reset_mail_ignore') }}</p>
                <p>{{ __('site.reset_mail_signoff', ['store' => config('app.name')]) }}</p>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding-top:20px;font-size:12px;color:#9ca3af;">
                <p>{{ __('site.reset_mail_fallback') }}</p>
                <p style="word-break:break-all;">{{ $resetUrl }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
