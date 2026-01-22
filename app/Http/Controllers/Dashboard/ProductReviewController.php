<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Dashboard\DashboardStatsService;

class ProductReviewController extends Controller
{

    public function __construct(
    protected DashboardStatsService $statsService
) {}
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'product_id' => (string) $request->input('product_id', ''),
            'rating' => (string) $request->input('rating', ''),
            'status' => (string) $request->input('status', ''),
        ];

        $query = Review::query()
            ->with([
                'product:id,slug,sku',
                'product.translations:id,product_id,locale,name',
                'user:id,name,email',
            ]);

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product.translations', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($p) use ($search) {
                        $p->where('sku', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['product_id'] !== '' && is_numeric($filters['product_id'])) {
            $query->where('product_id', (int) $filters['product_id']);
        }

        if ($filters['rating'] !== '' && is_numeric($filters['rating'])) {
            $query->where('rating', (int) $filters['rating']);
        }

        if ($filters['status'] === 'approved') {
            $query->where('is_approved', true);
        } elseif ($filters['status'] === 'pending') {
            $query->where('is_approved', false);
        }

        $reviews = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $products = Product::query()
            ->with('translations')
            ->orderByDesc('id')
            ->get();

        return view('dashboard.reviews.index', [
            'reviews' => $reviews,
            'products' => $products,
            'filters' => $filters,
            'canEdit' => $this->canCurrentUser('reviews.edit'),
            'canDelete' => $this->canCurrentUser('reviews.delete'),
        ]);
    }

    public function show(Review $review): View
    {
        $review->load([
            'product.translations',
            'product.primaryImage',
            'user',
        ]);

        return view('dashboard.reviews.show', [
            'review' => $review,
            'canEdit' => $this->canCurrentUser('reviews.edit'),
            'canDelete' => $this->canCurrentUser('reviews.delete'),
        ]);
    }

    public function productReviews(Product $product): View
    {
        $product->load([
            'translations',
            'allReviews.user',
        ]);

        return view('dashboard.reviews.product_reviews', [
            'product' => $product,
            'reviews' => $product->allReviews,
            'canEdit' => $this->canCurrentUser('reviews.edit'),
            'canDelete' => $this->canCurrentUser('reviews.delete'),
        ]);
    }

 public function toggleStatus(Review $review): RedirectResponse
{
    $review->update([
        'is_approved' => ! $review->is_approved,
    ]);

    $this->statsService->clear();

    return back()->with('success', __('Review status updated successfully.'));
}

public function bulkAction(Request $request): RedirectResponse
{
    $request->validate([
        'action' => ['required', 'string', 'in:approve,unapprove,delete'],
        'ids' => ['required', 'array', 'min:1'],
        'ids.*' => ['integer', 'exists:reviews,id'],
    ]);

    // Deleting needs the delete permission, the same as the single-item route.
    abort_if($request->input('action') === 'delete' && ! $request->user()->hasPermission('reviews.delete'), 403);

    $reviews = Review::whereIn('id', $request->ids)->get();

    foreach ($reviews as $review) {
        match ($request->action) {
            'approve' => $review->update(['is_approved' => true]),
            'unapprove' => $review->update(['is_approved' => false]),
            'delete' => $review->delete(),
        };
    }

    $this->statsService->clear();

    return back()->with('success', __('Bulk action applied successfully.'));
}

public function destroy(Review $review): RedirectResponse
{
    $review->delete();

    $this->statsService->clear();

    return back()->with('success', __('Review deleted successfully.'));
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
