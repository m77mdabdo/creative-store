<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تم إلغاء الطلب</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="650" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#111827;color:#ffffff;padding:24px 32px;font-size:24px;font-weight:bold;">
                            Creative Store
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin:0 0 16px;color:#111827;">تم إلغاء الطلب بنجاح</h2>

                            <p style="margin:0 0 16px;color:#374151;font-size:15px;">
                                مرحباً {{ $user->name ?? 'عميلنا العزيز' }}،
                            </p>

                            <p style="margin:0 0 24px;color:#374151;font-size:15px;line-height:1.8;">
                                نود إفادتك بأنه تم إلغاء طلبك بنجاح. تجد أدناه تفاصيل الطلب.
                            </p>

                            <table width="100%" cellpadding="12" cellspacing="0" style="border-collapse:collapse;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                                <tr style="background:#f9fafb;">
                                    <td style="border:1px solid #e5e7eb;font-weight:bold;">رقم الطلب</td>
                                    <td style="border:1px solid #e5e7eb;">#{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #e5e7eb;font-weight:bold;">تاريخ الطلب</td>
                                    <td style="border:1px solid #e5e7eb;">{{ $placedAt }}</td>
                                </tr>
                                <tr style="background:#f9fafb;">
                                    <td style="border:1px solid #e5e7eb;font-weight:bold;">إجمالي الطلب</td>
                                    <td style="border:1px solid #e5e7eb;">{{ $total }} {{ $currency }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #e5e7eb;font-weight:bold;">حالة الطلب</td>
                                    <td style="border:1px solid #e5e7eb;color:#b91c1c;font-weight:bold;">ملغي</td>
                                </tr>
                                <tr style="background:#f9fafb;">
                                    <td style="border:1px solid #e5e7eb;font-weight:bold;">سبب الإلغاء</td>
                                    <td style="border:1px solid #e5e7eb;">{{ $order->cancellation_reason ?: 'تم الإلغاء بواسطة العميل.' }}</td>
                                </tr>
                            </table>

                            @if($wasPaidOnline)
                                <div style="margin-top:24px;padding:18px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;color:#1e3a8a;">
                                    بما أن الطلب تم دفعه أونلاين، سيتم إرجاع المبلغ إلى المحفظة أو وسيلة الدفع خلال <strong>14 يوم</strong>.
                                </div>
                            @else
                                <div style="margin-top:24px;padding:18px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;color:#374151;">
                                    لم يتم تسجيل استرداد مالي أونلاين لهذا الطلب.
                                </div>
                            @endif

                            <p style="margin:24px 0 0;color:#6b7280;font-size:14px;line-height:1.8;">
                                إذا كان لديك أي استفسار، يرجى التواصل مع فريق الدعم.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f9fafb;padding:20px 32px;color:#6b7280;font-size:13px;text-align:center;">
                            © {{ now()->year }} Creative Store. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 
