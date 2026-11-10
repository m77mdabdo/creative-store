<?php

namespace App\Jobs;

use App\Models\AbandonedCartRecovery;
use App\Models\Cart;
use App\Notifications\CartReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Reminds customers (email and SMS/WhatsApp) about carts they left behind,
 * on a growing schedule: 10 minutes after the last cart change, then an
 * hour later, 2 hours later, 5 hours later and so on (store.cart_reminders).
 *
 * A "cycle" is the time since the cart last changed: changing the cart
 * starts a new cycle, recovering it or checking out ends it. Runs every
 * minute and works in bounded batches so time and memory stay flat however
 * many carts exist.
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

        $schedule = $this->schedule();

        if ($schedule === []) {
            return;
        }

        $processed = 0;

        Cart::query()
            ->select(['id', 'user_id', 'session_id', 'updated_at'])
            ->whereNotNull('user_id')
            ->whereBetween('updated_at', [now()->subDays(self::LOOKBACK_DAYS), now()->subMinutes($schedule[0])])
            ->whereHas('items')
            ->with([
                'user:id,name,email,phone',
                'recoveries' => fn ($q) => $q
                    ->select(['id', 'cart_id', 'status', 'cart_abandoned_at', 'created_at'])
                    ->where('created_at', '>=', now()->subDays(self::LOOKBACK_DAYS)),
            ])
            ->withCount('items')
            ->chunkById(self::BATCH_SIZE, function ($carts) use (&$processed, $schedule) {
                foreach ($carts as $cart) {
                    if (! $this->isDue($cart, $schedule)) {
                        continue;
                    }

                    $this->notify($cart, $this->cycle($cart)->count(), count($schedule));

                    if (++$processed >= self::MAX_PER_RUN) {
                        return false;
                    }
                }
            });
    }

    /**
     * @param  list<int>  $schedule  minutes to wait before each reminder
     */
    private function isDue(Cart $cart, array $schedule): bool
    {
        $cycle = $this->cycle($cart);

        if ($cycle->contains('status', 'recovered')) {
            return false;
        }

        $step = $cycle->count();

        if ($step >= count($schedule)) {
            return false;
        }

        $since = $step === 0 ? $cart->updated_at : $cycle->max('created_at');

        return $since !== null && $since->lessThanOrEqualTo(now()->subMinutes($schedule[$step]));
    }

    /**
     * Reminders already sent since the cart last changed.
     */
    private function cycle(Cart $cart): Collection
    {
        $changedAt = $cart->updated_at?->copy()->startOfSecond();

        return $cart->recoveries->filter(fn (AbandonedCartRecovery $recovery) => $changedAt === null
            || ($recovery->cart_abandoned_at !== null && $recovery->cart_abandoned_at->greaterThanOrEqualTo($changedAt)));
    }

    /** @return list<int> */
    private function schedule(): array
    {
        return array_values(array_filter(
            array_map('intval', (array) config('store.cart_reminders', [10, 60, 120, 300, 720, 1440])),
            fn (int $minutes) => $minutes > 0
        ));
    }

    private function notify(Cart $cart, int $step, int $steps): void
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
            'meta'              => [
                'items_count' => $cart->items_count,
                'step'        => $step + 1,
                'last'        => $step + 1 >= $steps,
            ],
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
