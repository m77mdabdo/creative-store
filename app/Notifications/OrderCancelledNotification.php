<?php

namespace App\Notifications;

use App\Models\NotificationLog;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order,
        protected bool $wasPaidOnline = false
    ) {
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $currency = $this->order->currency ?? 'SAR';
        $total = number_format((float) $this->order->total, 2);
        $placedAt = $this->order->created_at?->format('Y-m-d h:i A') ?? '-';
        $subject = 'تم إلغاء الطلب بنجاح #' . $this->order->order_number;

        NotificationLog::create([
            'user_id' => $notifiable->id ?? null,
            'order_id' => $this->order->id,
            'channel' => 'mail',
            'type' => 'order_cancelled',
            'status' => 'sent',
            'recipient' => $notifiable->email ?? null,
            'subject' => $subject,
            'message' => $this->wasPaidOnline
                ? 'تم إلغاء الطلب وسيتم رد المبلغ خلال 14 يوم.'
                : 'تم إلغاء الطلب بنجاح.',
            'meta' => [
                'order_number' => $this->order->order_number,
                'was_paid_online' => $this->wasPaidOnline,
            ],
            'sent_at' => now(),
        ]);

        $mail = (new MailMessage)
            ->subject($subject)
            ->view('emails.orders.cancelled', [
                'user' => $notifiable,
                'order' => $this->order,
                'wasPaidOnline' => $this->wasPaidOnline,
                'currency' => $currency,
                'total' => $total,
                'placedAt' => $placedAt,
            ]);

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        NotificationLog::create([
            'user_id' => $notifiable->id ?? null,
            'order_id' => $this->order->id,
            'channel' => 'database',
            'type' => 'order_cancelled',
            'status' => 'sent',
            'recipient' => $notifiable->email ?? null,
            'subject' => 'تم إلغاء الطلب',
            'message' => $this->wasPaidOnline
                ? 'تم إلغاء طلبك بنجاح. سيتم إرجاع المبلغ خلال 14 يوم.'
                : 'تم إلغاء طلبك بنجاح.',
            'meta' => [
                'order_number' => $this->order->order_number,
                'was_paid_online' => $this->wasPaidOnline,
            ],
            'sent_at' => now(),
        ]);

        return [
            'type' => 'order_cancelled',
            'title' => 'تم إلغاء الطلب بنجاح',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => (float) $this->order->total,
            'currency' => $this->order->currency ?? 'SAR',
            'status' => 'cancelled',
            'was_paid_online' => $this->wasPaidOnline,
            'message' => $this->wasPaidOnline
                ? 'تم إلغاء طلبك بنجاح. سيتم إرجاع المبلغ إلى وسيلة الدفع خلال 14 يوم.'
                : 'تم إلغاء طلبك بنجاح.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        NotificationLog::create([
            'user_id' => $notifiable->id ?? null,
            'order_id' => $this->order->id,
            'channel' => 'broadcast',
            'type' => 'order_cancelled',
            'status' => 'sent',
            'recipient' => $notifiable->email ?? null,
            'subject' => 'تم إلغاء الطلب',
            'message' => $this->wasPaidOnline
                ? 'تم إلغاء طلبك بنجاح. سيتم إرجاع المبلغ خلال 14 يوم.'
                : 'تم إلغاء طلبك بنجاح.',
            'meta' => [
                'order_number' => $this->order->order_number,
                'was_paid_online' => $this->wasPaidOnline,
            ],
            'sent_at' => now(),
        ]);

        return new BroadcastMessage([
            'type' => 'order_cancelled',
            'title' => 'تم إلغاء الطلب بنجاح',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'message' => $this->wasPaidOnline
                ? 'تم إلغاء طلبك بنجاح. سيتم إرجاع المبلغ إلى وسيلة الدفع خلال 14 يوم.'
                : 'تم إلغاء طلبك بنجاح.',
            'created_at' => now()->toDateTimeString(),
        ]);
    }
}
