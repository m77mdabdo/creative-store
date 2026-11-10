<?php

namespace App\Mail;

use App\Models\AbandonedCartRecovery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbandonedCartRecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AbandonedCartRecovery $recovery
    ) {
    }

    public function build(): self
    {
        $cart = $this->recovery->cart->loadMissing([
            'items.product.primaryImage',
            'items.product.translations',
            'items.variant',
            'user',
        ]);

        $step = (int) data_get($this->recovery->meta, 'step', 1);
        $tone = match (true) {
            (bool) data_get($this->recovery->meta, 'last', false) && $step > 1 => 'last',
            $step > 1 => 'followup',
            default => 'first',
        };

        return $this->subject(__('notifications.cart_email.subject_' . $tone))
            ->view('emails.abandoned-cart-recovery', [
                'recovery' => $this->recovery,
                'tone'     => $tone,
                'cart'     => $cart,
                'user'     => $cart->user,
                'recoverUrl' => route('cart.recover', $this->recovery->recovery_token),
            ]);
    }
}
