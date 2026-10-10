<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Keeps a signed-in customer's session cart saved in the database.
 *
 * The session cart is what the storefront reads; the saved copy lets the
 * cart follow the customer to another device or browser and lets the
 * reminder job know who left items behind. Only product, option and
 * quantity are stored: the cart page re-reads name, price and stock from
 * the catalogue whenever it is opened.
 */
class SavedCart
{
    public function for(User $user): ?Cart
    {
        return Cart::query()->where('user_id', $user->id)->latest('id')->first();
    }

    /**
     * Save the session cart for the user (an empty cart removes the saved one).
     */
    public function persist(User $user, array $sessionCart, ?string $sessionId = null): void
    {
        $lines = $this->lines($sessionCart);

        DB::transaction(function () use ($user, $lines, $sessionId) {
            $saved = Cart::query()->where('user_id', $user->id)->lockForUpdate()->get();

            if ($lines === []) {
                $saved->each->delete();

                return;
            }

            /** @var Cart $cart */
            $cart = $saved->sortByDesc('id')->first() ?? Cart::create(['user_id' => $user->id]);
            $saved->where('id', '!=', $cart->id)->each->delete();

            $cart->forceFill(['session_id' => $sessionId ?? $cart->session_id])->save();
            $cart->touch();

            $keep = [];

            foreach ($lines as $line) {
                $item = $cart->items()->updateOrCreate(
                    ['product_id' => $line['product_id'], 'variant_id' => $line['variant_id']],
                    ['quantity' => $line['quantity'], 'price_snapshot' => $line['price'], 'tax_rate_snapshot' => 0],
                );
                $keep[] = $item->id;
            }

            $cart->items()->whereNotIn('id', $keep)->delete();
        });
    }

    /**
     * Saved items as minimal session cart lines (the cart page fills in the rest).
     */
    public function restore(Cart $cart): array
    {
        $cart->loadMissing('items');
        $active = Product::query()->whereIn('id', $cart->items->pluck('product_id'))
            ->where('status', 'active')->pluck('id')->flip();

        $lines = [];

        foreach ($cart->items as $item) {
            if (! $active->has($item->product_id)) {
                continue;
            }

            $key = self::key($item->product_id, $item->variant_id);
            $lines[$key] = [
                'product_id' => (int) $item->product_id,
                'variant_id' => $item->variant_id ? (int) $item->variant_id : null,
                'quantity'   => (int) $item->quantity,
                'price'      => (float) $item->price_snapshot,
            ];
        }

        return $lines;
    }

    /**
     * Combine a guest's session cart with the customer's saved cart.
     * Items in both keep the larger quantity.
     */
    public function merge(array $sessionCart, array $savedLines): array
    {
        foreach ($savedLines as $key => $line) {
            if (isset($sessionCart[$key])) {
                $sessionCart[$key]['quantity'] = max((int) $sessionCart[$key]['quantity'], (int) $line['quantity']);
            } else {
                $sessionCart[$key] = $line;
            }
        }

        return $sessionCart;
    }

    /**
     * A short hash of what is in the cart, to detect changes cheaply.
     */
    public static function fingerprint(array $sessionCart): string
    {
        $parts = collect($sessionCart)
            ->map(fn ($item) => ($item['product_id'] ?? '') . ':' . ($item['variant_id'] ?? '') . ':' . (int) ($item['quantity'] ?? 0))
            ->sort()->implode('|');

        return md5($parts);
    }

    public static function key(int|string $productId, int|string|null $variantId): string
    {
        return $variantId ? $productId . '_' . $variantId : (string) $productId;
    }

    private function lines(array $sessionCart): array
    {
        return collect($sessionCart)
            ->filter(fn ($item) => ! empty($item['product_id']) && (int) ($item['quantity'] ?? 0) > 0)
            ->map(fn ($item) => [
                'product_id' => (int) $item['product_id'],
                'variant_id' => ! empty($item['variant_id']) ? (int) $item['variant_id'] : null,
                'quantity'   => min((int) $item['quantity'], 20),
                'price'      => round((float) ($item['price'] ?? 0), 2),
            ])
            ->values()
            ->all();
    }
}
