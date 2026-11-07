@php
    $rtl = app()->getLocale() === 'ar';
    $align = $rtl ? 'right' : 'left';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('staff.invite.subject', ['store' => config('app.name')]) }}</title>
</head>
<body style="margin:0; font-family: Tahoma, Arial, sans-serif; background:#f4f5fb; padding:30px 12px; direction:{{ $rtl ? 'rtl' : 'ltr' }};">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb; text-align:{{ $align }};">
        <div style="background:linear-gradient(135deg,#717fe0,#9b6cf0); padding:28px 30px; color:#ffffff;">
            <div style="font-size:13px; opacity:.85; margin-bottom:6px;">{{ config('app.name') }}</div>
            <h1 style="margin:0; font-size:22px; line-height:1.4;">{{ __('staff.invite.title', ['name' => $user->name]) }}</h1>
        </div>

        <div style="padding:28px 30px;">
            <p style="color:#374151; font-size:15px; line-height:1.8; margin:0 0 20px;">
                {{ $invitedBy
                    ? __('staff.invite.intro_by', ['by' => $invitedBy, 'role' => $role])
                    : __('staff.invite.intro', ['role' => $role]) }}
            </p>

            <h2 style="font-size:15px; color:#111827; margin:0 0 10px;">{{ __('staff.invite.details') }}</h2>
            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; background:#f9fafb; border-radius:12px;">
                <tr>
                    <td style="padding:12px 16px; color:#6b7280; font-size:13px; width:38%;">{{ __('staff.invite.login_page') }}</td>
                    <td style="padding:12px 16px; font-size:14px;" dir="ltr" align="{{ $align }}"><a href="{{ $loginUrl }}" style="color:#717fe0;">{{ $loginUrl }}</a></td>
                </tr>
                <tr>
                    <td style="padding:12px 16px; color:#6b7280; font-size:13px; border-top:1px solid #eef0f4;">{{ __('staff.invite.email') }}</td>
                    <td style="padding:12px 16px; font-size:14px; font-weight:bold; border-top:1px solid #eef0f4;" dir="ltr" align="{{ $align }}">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding:12px 16px; color:#6b7280; font-size:13px; border-top:1px solid #eef0f4;">{{ __('staff.invite.password') }}</td>
                    <td style="padding:12px 16px; font-size:13px; color:#374151; border-top:1px solid #eef0f4;">{{ __('staff.invite.password_hint') }}</td>
                </tr>
            </table>

            <div style="margin:28px 0 8px; text-align:center;">
                <a href="{{ $welcomeUrl }}"
                   style="display:inline-block; padding:14px 30px; background:#111827; color:#ffffff; text-decoration:none; border-radius:10px; font-weight:bold; font-size:15px;">
                    {{ __('staff.invite.button') }}
                </a>
            </div>

            <p style="color:#6b7280; font-size:12.5px; line-height:1.7; margin:20px 0 0;">
                {{ __('staff.invite.expiry', ['days' => $validDays]) }}<br>
                {{ __('staff.invite.ignore') }}
            </p>
        </div>
    </div>
</body>
</html>
