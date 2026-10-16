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

        return $this->subject(__('notifications.cart_email.subject'))
            ->view('emails.abandoned-cart-recovery', [
                'recovery' => $this->recovery,
                'cart'     => $cart,
                'user'     => $cart->user,
                'recoverUrl' => route('cart.recover', $this->recovery->recovery_token),
            ]);
    }
}
