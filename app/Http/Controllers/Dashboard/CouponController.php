<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Coupon\StoreCouponRequest;
use App\Http\Requests\Dashboard\Coupon\UpdateCouponRequest;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'scope' => (string) $request->input('scope', ''),
            'type' => (string) $request->input('type', ''),
            'status' => (string) $request->input('status', ''),
        ];

        $query = Coupon::query()
            ->with(['product.translations'])
            ->withCount(['usages', 'orders']);

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('product.translations', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['scope'] === 'cart') {
            $query->whereNull('product_id');
        } elseif ($filters['scope'] === 'product') {
            $query->whereNotNull('product_id');
        }

        if ($filters['type'] !== '') {
            $query->where('type', $filters['type']);
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        $coupons = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.coupons.index', [
            'coupons' => $coupons,
            'filters' => $filters,
            'canCreate' => $this->canCurrentUser('coupons.create'),
            'canEdit' => $this->canCurrentUser('coupons.edit'),
            'canDelete' => $this->canCurrentUser('coupons.delete'),
            'canViewTrash' => $this->canCurrentUser('coupons.view'),
        ]);
    }

    public function trash(): View
    {
        $coupons = Coupon::onlyTrashed()
            ->with(['product.translations'])
            ->withCount(['usages', 'orders'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('dashboard.coupons.trash', [
            'coupons' => $coupons,
            'canEdit' => $this->canCurrentUser('coupons.edit'),
            'canDelete' => $this->canCurrentUser('coupons.delete'),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.coupons.create', [
            'products' => $this->productsList(),
            'form' => $this->formData(),
        ]);
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        Coupon::create($this->normalizedPayload($request));

        return redirect()
            ->route('dashboard.coupons.index')
            ->with('success', __('Coupon created successfully.'));
    }

    public function show(Coupon $coupon): View
    {
        $coupon->load([
            'product.translations',
            'usages.user',
            'usages.order',
        ])->loadCount(['usages', 'orders']);

        return view('dashboard.coupons.show', [
            'coupon' => $coupon,
            'canEdit' => $this->canCurrentUser('coupons.edit'),
            'canDelete' => $this->canCurrentUser('coupons.delete'),
        ]);
    }

    public function edit(Coupon $coupon): View
    {
        return view('dashboard.coupons.edit', [
            'coupon' => $coupon,
            'products' => $this->productsList(),
            'form' => $this->formData($coupon),
        ]);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->normalizedPayload($request));

        return redirect()
            ->route('dashboard.coupons.index')
            ->with('success', __('Coupon updated successfully.'));
    }

    public function toggleStatus(Coupon $coupon): RedirectResponse
    {
        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        return back()->with('success', __('Coupon status updated successfully.'));
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()
            ->route('dashboard.coupons.index')
            ->with('success', __('Coupon moved to trash successfully.'));
    }

    public function restore(int $id): RedirectResponse
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);
        $coupon->restore();

        return redirect()
            ->route('dashboard.coupons.trash')
            ->with('success', __('Coupon restored successfully.'));
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $coupon = Coupon::onlyTrashed()->findOrFail($id);
        $coupon->forceDelete();

        return redirect()
            ->route('dashboard.coupons.trash')
            ->with('success', __('Coupon permanently deleted.'));
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:coupons,id'],
        ]);

        // Deleting needs the delete permission, the same as the single-item route.
        abort_if($request->input('action') === 'delete' && ! $request->user()->hasPermission('coupons.delete'), 403);

        $coupons = Coupon::whereIn('id', $request->input('ids', []))->get();

        foreach ($coupons as $coupon) {
            match ($request->input('action')) {
                'activate' => $coupon->update(['is_active' => true]),
                'deactivate' => $coupon->update(['is_active' => false]),
                'delete' => $coupon->delete(),
            };
        }

        return back()->with('success', __('Bulk action applied successfully.'));
    }

    private function productsList()
    {
        return Product::query()
            ->with('translations')
            ->orderByDesc('id')
            ->get();
    }

    private function formData(?Coupon $coupon = null): array
    {
        return [
            'product_id' => old('product_id', $coupon->product_id ?? ''),
            'code' => old('code', $coupon->code ?? ''),
            'type' => old('type', $coupon->type ?? 'fixed'),
            'value' => old('value', $coupon->value ?? ''),
            'min_order_amount' => old('min_order_amount', $coupon->min_order_amount ?? ''),
            'max_discount_amount' => old('max_discount_amount', $coupon->max_discount_amount ?? ''),
            'usage_limit' => old('usage_limit', $coupon->usage_limit ?? ''),
            'per_user_limit' => old('per_user_limit', $coupon->per_user_limit ?? ''),
            'starts_at' => old('starts_at', optional($coupon?->starts_at)->format('Y-m-d\TH:i')),
            'expires_at' => old('expires_at', optional($coupon?->expires_at)->format('Y-m-d\TH:i')),
            'is_active' => old('is_active', $coupon?->is_active ?? true),
        ];
    }

    private function normalizedPayload(Request $request): array
    {
        return [
            'product_id' => $request->filled('product_id') ? (int) $request->input('product_id') : null,
            'code' => strtoupper(trim((string) $request->input('code'))),
            'type' => (string) $request->input('type'),
            'value' => (float) $request->input('value'),
            'min_order_amount' => $request->filled('min_order_amount') ? (float) $request->input('min_order_amount') : null,
            'max_discount_amount' => $request->filled('max_discount_amount') ? (float) $request->input('max_discount_amount') : null,
            'usage_limit' => $request->filled('usage_limit') ? (int) $request->input('usage_limit') : null,
            'per_user_limit' => $request->filled('per_user_limit') ? (int) $request->input('per_user_limit') : null,
            'starts_at' => $request->filled('starts_at') ? $request->input('starts_at') : null,
            'expires_at' => $request->filled('expires_at') ? $request->input('expires_at') : null,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function canCurrentUser(string $permission): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('owner')
            || $user->hasRole('admin')
            || $user->hasPermission($permission);
    }
}
