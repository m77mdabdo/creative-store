<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Cart\CartIndexRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartIndexRequest $request): View
    {
        return $this->renderCartListing($request, false);
    }

    public function abandoned(CartIndexRequest $request): View
    {
        return $this->renderCartListing($request, true);
    }

    protected function renderCartListing(CartIndexRequest $request, bool $abandonedOnly = false): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $dateFrom = !empty($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : null;

        $dateTo = !empty($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : null;

        $abandonedThreshold = now()->subHours(24);

        $query = Cart::query()
            ->with([
                'user:id,name,email,phone',
            ])
            ->withCount('items');

        $query->when(!empty($filters['q']), function ($q) use ($filters) {
            $search = trim((string) $filters['q']);

            $q->where(function ($cartQuery) use ($search) {
                $cartQuery->where('session_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        });

        $query->when(!empty($filters['type']), function ($q) use ($filters) {
            if ($filters['type'] === 'user') {
                $q->whereNotNull('user_id');
            } else {
                $q->whereNull('user_id');
            }
        });

        $query->when(!empty($filters['status']), function ($q) use ($filters, $abandonedThreshold) {
            if ($filters['status'] === 'abandoned') {
                $q->where('updated_at', '<', $abandonedThreshold)
                  ->has('items');
            } else {
                $q->where('updated_at', '>=', $abandonedThreshold)
                  ->has('items');
            }
        });

        if ($abandonedOnly) {
            $query->where('updated_at', '<', $abandonedThreshold)
                  ->has('items');
        }

        $query->when(isset($filters['min_items']) && $filters['min_items'] !== '', function ($q) use ($filters) {
            $q->has('items', '>=', (int) $filters['min_items']);
        });

        $query->when(!empty($filters['product_id']), function ($q) use ($filters) {
            $q->whereHas('items', function ($itemQuery) use ($filters) {
                $itemQuery->where('product_id', $filters['product_id']);
            });
        });

        $query->when(!empty($filters['product_status']), function ($q) use ($filters) {
            $q->whereHas('items.product', function ($productQuery) use ($filters) {
                if ($filters['product_status'] === 'active') {
                    $productQuery->where('status', 'active');
                } else {
                    $productQuery->where('status', '!=', 'active');
                }
            });
        });

        $query->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom));
        $query->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo));

        $sort = $filters['sort'] ?? 'latest';

        match ($sort) {
            'oldest'     => $query->oldest(),
            'items_asc'  => $query->withCount('items')->orderBy('items_count'),
            'items_desc' => $query->withCount('items')->orderByDesc('items_count'),
            default      => $query->latest(),
        };

        $carts = $query->paginate($perPage)->withQueryString();

        $cartItemsBase = CartItem::query()
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

        $cartsBase = Cart::query()
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('created_at', '<=', $dateTo));

        $stats = [
            'total_carts'        => (clone $cartsBase)->count(),
            'active_carts'       => Cart::query()->where('updated_at', '>=', $abandonedThreshold)->has('items')->count(),
            'abandoned_carts'    => Cart::query()->where('updated_at', '<', $abandonedThreshold)->has('items')->count(),
            'guest_carts'        => (clone $cartsBase)->whereNull('user_id')->count(),
            'user_carts'         => (clone $cartsBase)->whereNotNull('user_id')->count(),
            'total_cart_items'   => (clone $cartItemsBase)->count(),
        ];

        $topProductsRaw = (clone $cartItemsBase)
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
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
                'count'   => (int) $row->total_qty,
            ];
        })->filter(fn ($row) => !is_null($row['product']))->values();

        $analytics = (clone $cartItemsBase)
            ->selectRaw('DATE(created_at) as day, SUM(quantity) as total_qty, COUNT(*) as total_rows')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day')
            ->get();

        $productsFilter = Product::query()
            ->select('id', 'slug', 'sku', 'status')
            ->with(['translations'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('dashboard.carts.index', compact(
            'carts',
            'filters',
            'stats',
            'topProducts',
            'analytics',
            'productsFilter',
            'abandonedOnly'
        ));
    }

    public function show(Cart $cart): View
    {
        $cart->load([
            'user:id,name,email,phone',
            'items' => fn ($q) => $q->latest(),
            'items.product:id,slug,sku,base_price,currency,status,tax_rate',
            'items.product.translations:id,product_id,locale,name',
            'items.product.primaryImage:id,product_id,path,is_primary',
            'items.variant:id,product_id,sku,price,stock,is_active',
        ]);

        $summary = [
            'items_count'      => $cart->items->count(),
            'quantity_total'   => $cart->items->sum('quantity'),
            'subtotal'         => $cart->items->sum(fn ($item) => (float) $item->price_snapshot * (int) $item->quantity),
            'is_abandoned'     => $cart->updated_at < now()->subHours(24) && $cart->items->count() > 0,
            'has_user_orders'  => $cart->user ? $cart->user->orders()->exists() : false,
            'orders_count'     => $cart->user ? $cart->user->orders()->count() : 0,
        ];

        return view('dashboard.carts.show', compact('cart', 'summary'));
    }

    public function destroy(Cart $cart): RedirectResponse
    {
        $cart->delete();

        return redirect()
            ->route('dashboard.carts.index')
            ->with('success', __('Cart deleted successfully.'));
    }

    public function destroyItem(Cart $cart, CartItem $item): RedirectResponse
    {
        if ($item->cart_id !== $cart->id) {
            abort(404);
        }

        $item->delete();

        return back()->with('success', __('Cart item deleted successfully.'));
    }
}
