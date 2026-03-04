<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The original table meant one wishlist per customer, but the unique flag
 * was attached to the foreign key instead of the column, so no index was
 * created. Folds any extra wishlists into the customer's first one, then
 * adds the index.
 */
return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('wishlists')
            ->select('user_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $row) {
            $extraIds = DB::table('wishlists')
                ->where('user_id', $row->user_id)
                ->where('id', '!=', $row->keep_id)
                ->pluck('id');

            DB::table('wishlist_items')->whereIn('wishlist_id', $extraIds)->orderBy('id')->get()
                ->each(function ($item) use ($row) {
                    $exists = DB::table('wishlist_items')
                        ->where('wishlist_id', $row->keep_id)
                        ->where('product_id', $item->product_id)
                        ->where(fn ($q) => $item->variant_id === null
                            ? $q->whereNull('variant_id')
                            : $q->where('variant_id', $item->variant_id))
                        ->exists();

                    $exists
                        ? DB::table('wishlist_items')->where('id', $item->id)->delete()
                        : DB::table('wishlist_items')->where('id', $item->id)->update(['wishlist_id' => $row->keep_id]);
                });

            DB::table('wishlists')->whereIn('id', $extraIds)->delete();
        }

        Schema::table('wishlists', function (Blueprint $table) {
            $table->unique('user_id', 'wishlists_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropUnique('wishlists_user_id_unique');
        });
    }
};
