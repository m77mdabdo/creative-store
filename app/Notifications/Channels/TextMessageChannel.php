<?php

namespace App\Notifications\Channels;

use App\Services\Messaging\Messenger;
use App\Services\Messaging\TextMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Notification channel "text" (SMS or WhatsApp, per config/messaging.php).
 * A failed send is logged and never breaks the order flow.
 */
class TextMessageChannel
{
    public function __construct(private readonly Messenger $messenger)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        /** @var TextMessage $message */
        $message = $notification->toText($notifiable);
        $to = $message->to ?? $notifiable->routeNotificationFor('text', $notification) ?? $notifiable->phone ?? null;

        if (! $to) {
            return;
        }

        try {
            $this->messenger->send($to, $message->body);
        } catch (Throwable $e) {
            Log::warning('Text message failed', ['error' => $e->getMessage(), 'notification' => $notification::class]);
        }
    }
}
