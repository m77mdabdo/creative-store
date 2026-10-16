<?php

namespace App\Notifications;

use App\Mail\AbandonedCartRecoveryMail;
use App\Models\AbandonedCartRecovery;
use App\Notifications\Channels\TextMessageChannel;
use App\Services\Messaging\TextMessage;
use Illuminate\Notifications\Notification;

/**
 * "You left items in your cart" by email and SMS/WhatsApp, with a link
 * that restores the cart on any device.
 */
class CartReminderNotification extends Notification
{
    public function __construct(public readonly AbandonedCartRecovery $recovery)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', TextMessageChannel::class];
    }

    public function toMail(object $notifiable): AbandonedCartRecoveryMail
    {
        return (new AbandonedCartRecoveryMail($this->recovery))->to($notifiable->email);
    }

    public function toText(object $notifiable): TextMessage
    {
        $count = (int) data_get($this->recovery->meta, 'items_count', 0);

        return new TextMessage(trans_choice('notifications.text.cart_reminder', $count, [
            'count' => $count,
            'link'  => route('cart.recover', $this->recovery->recovery_token),
        ], config('app.locale')));
    }
}
