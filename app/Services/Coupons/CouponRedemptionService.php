<?php

namespace App\Services\Coupons;

use App\Exceptions\CheckoutException;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;

/**
 * Records and releases coupon usage atomically.
 *
 * Coupon rows are locked in id order before the limits are checked, so two
 * customers redeeming the last remaining use at the same moment cannot both
 * succeed, and concurrent checkouts never deadlock on each other.
 *
 * Both methods expect to run inside an open database transaction.
 */
class CouponRedemptionService
{
    /**
     * @param  array<int, float>  $discounts  coupon id => discount granted on this order
     */
    public function redeem(Order $order, array $discounts): void
    {
        if ($discounts === []) {
            return;
        }

        $coupons = Coupon::query()
            ->whereIn('id', array_keys($discounts))
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($coupons->count() !== count($discounts)) {
            throw new CheckoutException(__('A coupon in your cart is no longer available.'));
        }

        foreach ($coupons as $coupon) {
            $this->assertRedeemable($coupon, $order->user_id);

            $coupon->increment('used_count');

            CouponUsage::create([
                'coupon_id'       => $coupon->id,
                'user_id'         => $order->user_id,
                'order_id'        => $order->id,
                'discount_amount' => round((float) $discounts[$coupon->id], 2),
            ]);
        }
    }

    /**
     * Give back the uses an order consumed (cancellation, expiry).
     */
    public function release(Order $order): void
    {
        $usages = CouponUsage::query()->where('order_id', $order->id)->get();

        if ($usages->isEmpty()) {
            return;
        }

        Coupon::query()
            ->whereIn('id', $usages->pluck('coupon_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->each(function (Coupon $coupon) {
                if ($coupon->used_count > 0) {
                    $coupon->decrement('used_count');
                }
            });

        CouponUsage::query()->where('order_id', $order->id)->delete();
    }

    private function assertRedeemable(Coupon $coupon, ?int $userId): void
    {
        $now = now();

        $valid = $coupon->is_active
            && (! $coupon->starts_at || $now->gte($coupon->starts_at))
            && (! $coupon->expires_at || $now->lte($coupon->expires_at))
            && (is_null($coupon->usage_limit) || $coupon->used_count < $coupon->usage_limit);

        if ($valid && $userId && ! is_null($coupon->per_user_limit)) {
            $usedByUser = CouponUsage::query()
                ->where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();

            $valid = $usedByUser < $coupon->per_user_limit;
        }

        if (! $valid) {
            throw new CheckoutException(__('Coupon :code can no longer be used. Please remove it and try again.', [
                'code' => $coupon->code,
            ]));
        }
    }
}
