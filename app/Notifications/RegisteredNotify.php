<?php

namespace App\Notifications;

use App\Notifications\Concerns\WritesBellImmediately;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RegisteredNotify extends Notification implements ShouldQueue
{
    use Queueable;
    use WritesBellImmediately;

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم إنشاء حسابك بنجاح ✅')
            ->greeting('مرحباً ' . ($notifiable->name ?? ''))
            ->line('تم إنشاء حساب جديد باستخدام بريدك الإلكتروني.')
            ->line('التاريخ: ' . now()->format('Y-m-d H:i'))
            ->line('IP: ' . request()->ip())
            ->line('إذا لم تكن أنت، تجاهل الرسالة أو تواصل مع الدعم.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'تم إنشاء حسابك بنجاح',
            'message' => 'تم إنشاء حساب جديد باستخدام بريدك الإلكتروني.',
            'ip' => request()->ip(),
            'at' => now()->toDateTimeString(),
        ];
    }
}
