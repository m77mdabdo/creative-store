<?php

namespace App\Notifications;

use App\Notifications\Concerns\WritesBellImmediately;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LoginNotify extends Notification implements ShouldQueue
{
    use Queueable;
    use WritesBellImmediately;

    public function __construct(
        public ?string $driver = null // مثال: "google" أو "web"
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تنبيه تسجيل دخول 🔐')
            ->greeting('مرحباً ' . ($notifiable->name ?? ''))
            ->line('تم تسجيل الدخول إلى حسابك بنجاح.')
            ->line('الطريقة: ' . ($this->driver ?? 'web'))
            ->line('التاريخ: ' . now()->format('Y-m-d H:i'))
            ->line('IP: ' . request()->ip())
            ->line('إذا لم تكن أنت، غيّر كلمة المرور فوراً.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'تم تسجيل الدخول',
            'message' => 'تم تسجيل الدخول إلى حسابك بنجاح.',
            'driver' => $this->driver ?? 'web',
            'ip' => request()->ip(),
            'at' => now()->toDateTimeString(),
        ];
    }
}
