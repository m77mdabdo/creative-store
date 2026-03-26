<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Wishlist\WishlistIndexRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(WishlistIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $dateFrom = !empty($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : null;

        $dateTo = !empty($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : null;

        $query = Wishlist::query()
            ->with([
                'user:id,name,email,phone',
            ])
            ->withCount('items');

        $query->when(!empty($filters['q']), function ($q) use ($filters) {
            $search = trim((string) $filters['q']);

            $q->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        $query->when(isset($filters['min_items']) && $filters['min_items'] !== '', function ($q) use ($filters) {
            $q->has('items', '>=', (int) $filters['min_items']);
        });

        $query->when(!empty($filters['product_id']), function ($q) use ($filters) {
            $q->whereHas('items', function ($itemQuery) use ($filters) {
                $itemQuery->where('product_id', $filters['product_id']);
            });
        });

        $query->when(!empty($filters['product_status']), function ($q) use ($filters) {
            $status = $filters['product_status'] === 'active' ? 'active' : '!=';
            if ($filters['product_status'] === 'active') {
                $q->whereHas('items.product', fn ($productQuery) => $productQuery->where('status', 'active'));
            } else {
                $q->whereHas('items.product', fn ($productQuery) => $productQuery->where('status', '!=', 'active'));
            }
        });

        $query->when($dateFrom || $dateTo, function ($q) use ($dateFrom, $dateTo) {
            $q->whereHas('items', function ($itemQuery) use ($dateFrom, $dateTo) {
                if ($dateFrom) {
                    $itemQuery->where('wishlist_items.created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $itemQuery->where('wishlist_items.created_at', '<=', $dateTo);
                }
            });
        });

        $sort = $filters['sort'] ?? 'latest';

        match ($sort) {
            'oldest'         => $query->oldest(),
            'items_asc'      => $query->withCount('items')->orderBy('items_count'),
            'items_desc'     => $query->withCount('items')->orderByDesc('items_count'),
            'user_name_asc'  => $query->join('users', 'wishlists.user_id', '=', 'users.id')
                                      ->select('wishlists.*')
                                      ->orderBy('users.name'),
            'user_name_desc' => $query->join('users', 'wishlists.user_id', '=', 'users.id')
                                      ->select('wishlists.*')
                                      ->orderByDesc('users.name'),
            default          => $query->latest(),
        };

        $wishlists = $query->paginate($perPage)->withQueryString();

        $wishlistItemsBase = WishlistItem::query()
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo))
            ->when(!empty($filters['product_id']), fn ($q) => $q->where('product_id', $filters['product_id']))
            ->when(!empty($filters['product_status']), function ($q) use ($filters) {
                $q->whereHas('product', function ($productQuery) use ($filters) {
                    if ($filters['product_status'] === 'active') {
                        $productQuery->where('status', 'active');
                    } else {
                        $productQuery->where('status', '!=', 'active');
                    }
                });
            });

        $stats = [
            'total_wishlists'      => Wishlist::count(),
            'total_wishlist_items' => (clone $wishlistItemsBase)->count(),
            'non_empty_wishlists'  => Wishlist::has('items')->count(),
            'empty_wishlists'      => Wishlist::doesntHave('items')->count(),
        ];

        $topProductsRaw = (clone $wishlistItemsBase)
            ->select('product_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $topProductIds = $topProductsRaw->pluck('product_id')->filter()->values();

        $products = Product::query()
            ->with(['primaryImage', 'translations'])
            ->whereIn('id', $topProductIds)
            ->get()
            ->keyBy('id');

        $topProducts = $topProductsRaw->map(function ($row) use ($products) {
            $product = $products->get($row->product_id);

            return [
                'product' => $product,
                'count'   => (int) $row->total,
            ];
        })->filter(fn ($row) => !is_null($row['product']))->values();

        $topUsers = Wishlist::query()
            ->join('users', 'wishlists.user_id', '=', 'users.id')
            ->leftJoin('wishlist_items', 'wishlists.id', '=', 'wishlist_items.wishlist_id')
            ->when($dateFrom, fn ($q) => $q->where('wishlist_items.created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('wishlist_items.created_at', '<=', $dateTo))
            ->when(!empty($filters['product_id']), fn ($q) => $q->where('wishlist_items.product_id', $filters['product_id']))
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(wishlist_items.id) as wishlist_items_count')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('wishlist_items_count')
            ->limit(8)
            ->get();

        $analytics = (clone $wishlistItemsBase)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total_items')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day')
            ->get();

        $productsFilter = Product::query()
            ->select('id', 'slug', 'sku', 'status')
            ->with(['translations'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('dashboard.wishlists.index', compact(
            'wishlists',
            'filters',
            'stats',
            'topProducts',
            'topUsers',
            'analytics',
            'productsFilter'
        ));
    }

    public function show(Wishlist $wishlist): View
    {
        $wishlist->load([
            'user:id,name,email,phone',
            'items' => fn ($q) => $q->latest(),
            'items.product:id,slug,sku,base_price,currency,status',
            'items.product.translations:id,product_id,locale,name',
            'items.product.primaryImage:id,product_id,path,is_primary',
            'items.variant:id,product_id,sku,price,stock,is_active',
        ]);

        return view('dashboard.wishlists.show', compact('wishlist'));
    }

    public function destroy(Wishlist $wishlist): RedirectResponse
    {
        $wishlist->delete();

        return redirect()
            ->route('dashboard.wishlists.index')
            ->with('success', 'Wishlist deleted successfully.');
    }

    public function destroyItem(Wishlist $wishlist, WishlistItem $item): RedirectResponse
    {
        if ($item->wishlist_id !== $wishlist->id) {
            abort(404);
        }

        $item->delete();

        return back()->with('success', 'Wishlist item deleted successfully.');
    }

    public function moveAllToCart(Wishlist $wishlist): RedirectResponse
    {
        $wishlist->load(['items.product', 'items.variant']);

        if (!$wishlist->user_id) {
            return back()->with('error', 'This wishlist is not linked to a valid user.');
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $wishlist->user_id,
        ], [
            'session_id' => null,
        ]);

        DB::transaction(function () use ($wishlist, $cart) {
            foreach ($wishlist->items as $item) {
                if (!$item->product) {
                    continue;
                }

                $price = $item->variant?->price ?? $item->product->base_price ?? 0;
                $taxRate = $item->product->tax_rate ?? 0;

                $cartItem = CartItem::query()
                    ->where('cart_id', $cart->id)
                    ->where('product_id', $item->product_id)
                    ->where(function ($q) use ($item) {
                        if ($item->variant_id) {
                            $q->where('variant_id', $item->variant_id);
                        } else {
                            $q->whereNull('variant_id');
                        }
                    })
                    ->first();

                if ($cartItem) {
                    $cartItem->increment('quantity', 1);
                } else {
                    CartItem::create([
                        'cart_id'            => $cart->id,
                        'product_id'         => $item->product_id,
                        'variant_id'         => $item->variant_id,
                        'quantity'           => 1,
                        'price_snapshot'     => $price,
                        'tax_rate_snapshot'  => $taxRate,
                    ]);
                }
            }
        });

        return back()->with('success', 'All wishlist items moved to cart successfully.');
    }

    public function moveItemToCart(Wishlist $wishlist, WishlistItem $item): RedirectResponse
    {
        if ($item->wishlist_id !== $wishlist->id) {
            abort(404);
        }

        $wishlist->loadMissing('user');
        $item->loadMissing(['product', 'variant']);

        if (!$wishlist->user_id || !$item->product) {
            return back()->with('error', 'Cannot move this item to cart.');
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $wishlist->user_id,
        ], [
            'session_id' => null,
        ]);

        DB::transaction(function () use ($cart, $item) {
            $price = $item->variant?->price ?? $item->product->base_price ?? 0;
            $taxRate = $item->product->tax_rate ?? 0;

            $cartItem = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $item->product_id)
                ->where(function ($q) use ($item) {
                    if ($item->variant_id) {
                        $q->where('variant_id', $item->variant_id);
                    } else {
                        $q->whereNull('variant_id');
                    }
                })
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', 1);
            } else {
                CartItem::create([
                    'cart_id'            => $cart->id,
                    'product_id'         => $item->product_id,
                    'variant_id'         => $item->variant_id,
                    'quantity'           => 1,
                    'price_snapshot'     => $price,
                    'tax_rate_snapshot'  => $taxRate,
                ]);
            }

            $item->delete();
        });

        return back()->with('success', 'Wishlist item moved to cart successfully.');
    }
}
