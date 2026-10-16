<?php

namespace App\Jobs;

use App\Models\AbandonedCartRecovery;
use App\Models\Cart;
use App\Notifications\CartReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

/**
 * Reminds customers (email and SMS/WhatsApp) about carts they left behind.
 *
 * Works in bounded batches so the run time and memory stay flat no matter
 * how many carts exist: only carts abandoned in the recent window are
 * scanned, those already contacted are excluded in SQL, and each run
 * handles at most MAX_PER_RUN carts.
 */
class ProcessAbandonedCarts implements ShouldQueue
{
    use Queueable;

    private const LOOKBACK_DAYS = 7;

    private const BATCH_SIZE = 200;

    private const MAX_PER_RUN = 1000;

    public function handle(): void
    {
        $this->expireOldRecoveries();

        $processed = 0;

        Cart::query()
            ->select(['id', 'user_id', 'session_id', 'updated_at'])
            ->whereNotNull('user_id')
            ->whereBetween('updated_at', [
                now()->subDays(self::LOOKBACK_DAYS),
                now()->subHours(max(1, (int) config('store.cart_reminder_after_hours', 3))),
            ])
            ->whereHas('items')
            ->whereNotExists(fn (QueryBuilder $q) => $q
                ->selectRaw('1')
                ->from('abandoned_cart_recoveries')
                ->whereColumn('abandoned_cart_recoveries.cart_id', 'carts.id')
                ->whereIn('abandoned_cart_recoveries.status', ['pending', 'sent', 'recovered']))
            ->with(['user:id,name,email,phone'])
            ->withCount('items')
            ->chunkById(self::BATCH_SIZE, function ($carts) use (&$processed) {
                foreach ($carts as $cart) {
                    $this->notify($cart);

                    if (++$processed >= self::MAX_PER_RUN) {
                        return false;
                    }
                }
            });
    }

    private function notify(Cart $cart): void
    {
        $email = $cart->user?->email;

        if (! $email) {
            return;
        }

        $recovery = AbandonedCartRecovery::create([
            'cart_id'           => $cart->id,
            'user_id'           => $cart->user_id,
            'email'             => $email,
            'session_id'        => $cart->session_id,
            'channel'           => 'email',
            'status'            => 'pending',
            'recovery_token'    => Str::random(64),
            'cart_abandoned_at' => $cart->updated_at,
            'expires_at'        => now()->addDays(3),
            'meta'              => ['items_count' => $cart->items_count],
        ]);

        try {
            $cart->user->notify(new CartReminderNotification($recovery));

            $recovery->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            report($e);

            $recovery->update(['status' => 'failed', 'note' => mb_substr($e->getMessage(), 0, 500)]);
        }
    }

    private function expireOldRecoveries(): void
    {
        AbandonedCartRecovery::query()
            ->whereIn('status', ['pending', 'sent'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }
}
