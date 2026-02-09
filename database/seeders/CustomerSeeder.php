<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Address;
use App\Models\Review;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * تأكيد وجود Users
         * ========================= */
        if (User::count() < 5) {
            User::factory(10)->create();
        }

        /* =========================
         * تأكيد وجود Products
         * ========================= */
        if (Product::count() === 0) {
            $this->command->warn('No products found. Run CatalogSeeder first.');
            return;
        }

        $users = User::all();
        $products = Product::all();

        foreach ($users as $user) {

            /* =========================
             * Cart (واحد لكل user)
             * ========================= */
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['session_id' => null]
            );

            /* =========================
             * Cart Items (unique cart+product+variant)
             * ========================= */
            $cartItemsCount = rand(1, 4);

            $pickedProducts = $products->random(min($cartItemsCount, $products->count()));
            $pickedProducts = $pickedProducts instanceof \Illuminate\Support\Collection
                ? $pickedProducts
                : collect([$pickedProducts]);

            foreach ($pickedProducts as $product) {
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'variant_id' => null,
                    ],
                    [
                        'quantity' => rand(1, 3),
                        'price_snapshot' => $product->base_price ?? fake()->randomFloat(2, 20, 300),
                        'tax_rate_snapshot' => $product->tax_rate ?? 15,
                    ]
                );
            }

            /* =========================
             * Wishlist (واحد لكل user)
             * ========================= */
            $wishlist = Wishlist::firstOrCreate([
                'user_id' => $user->id,
            ]);

            /* =========================
             * Wishlist Items (unique wishlist+product+variant)
             * ========================= */
            $wishlistCount = rand(1, 5);

            $pickedWishlistProducts = $products->random(min($wishlistCount, $products->count()));
            $pickedWishlistProducts = $pickedWishlistProducts instanceof \Illuminate\Support\Collection
                ? $pickedWishlistProducts
                : collect([$pickedWishlistProducts]);

            foreach ($pickedWishlistProducts as $product) {
                WishlistItem::updateOrCreate(
                    [
                        'wishlist_id' => $wishlist->id,
                        'product_id' => $product->id,
                        'variant_id' => null,
                    ],
                    []
                );
            }

            /* =========================
             * Addresses
             * ========================= */
            // بدل ما نعمل duplicates كتير، نعمل 1-2 لكن باستخدام factory عادي (مفيش unique constraint هنا)
            Address::factory()
                ->count(rand(1, 2))
                ->create([
                    'user_id' => $user->id,
                    'country' => 'Saudi Arabia',
                ]);

            /* =========================
             * Reviews (unique user+product)
             * ========================= */
            $reviewsCount = rand(0, 3);

            if ($reviewsCount > 0) {
                $pickedReviewProducts = $products->random(min($reviewsCount, $products->count()));
                $pickedReviewProducts = $pickedReviewProducts instanceof \Illuminate\Support\Collection
                    ? $pickedReviewProducts
                    : collect([$pickedReviewProducts]);

                foreach ($pickedReviewProducts as $product) {
                    Review::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'product_id' => $product->id,
                        ],
                        [
                            'rating' => rand(3, 5),
                            'title' => fake()->optional()->sentence(4),
                            'comment' => fake()->optional()->paragraph(),
                            'is_approved' => true,
                        ]
                    );
                }
            }
        }
    }
}
