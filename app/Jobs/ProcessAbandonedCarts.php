<?php

namespace App\Jobs;

use App\Mail\AbandonedCartRecoveryMail;
use App\Models\AbandonedCartRecovery;
use App\Models\Cart;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProcessAbandonedCarts implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $abandonedThreshold = now()->subHours(24);

        $carts = Cart::query()
            ->with([
                'user:id,name,email',
                'items.product:id,status',
            ])
            ->where('updated_at', '<', $abandonedThreshold)
            ->whereHas('items')
            ->get();

        foreach ($carts as $cart) {
            $email = $cart->user?->email;

            if (!$email) {
                continue;
            }

            $alreadyRecoveredOrSent = AbandonedCartRecovery::query()
                ->where('cart_id', $cart->id)
                ->whereIn('status', ['pending', 'sent', 'recovered'])
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
                })
                ->exists();

            if ($alreadyRecoveredOrSent) {
                continue;
            }

            $recovery = AbandonedCartRecovery::create([
                'cart_id'            => $cart->id,
                'user_id'            => $cart->user_id,
                'email'              => $email,
                'session_id'         => $cart->session_id,
                'channel'            => 'email',
                'status'             => 'pending',
                'recovery_token'     => Str::uuid() . '-' . Str::random(2),
                'cart_abandoned_at'  => $cart->updated_at,
                'expires_at'         => now()->addDays(3),
                'meta'               => [
                    'items_count' => $cart->items->count(),
                ],
            ]);

            try {
                Mail::to($email)->send(new AbandonedCartRecoveryMail($recovery));

                $recovery->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                ]);
            } catch (\Throwable $e) {
                $recovery->update([
                    'status' => 'failed',
                    'note'   => $e->getMessage(),
                ]);
            }
        }

        AbandonedCartRecovery::query()
            ->whereIn('status', ['pending', 'sent'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'expired',
            ]);
    }
}
