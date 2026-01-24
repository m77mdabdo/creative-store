<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Category\StoreCategoryRequest;
use App\Http\Requests\Dashboard\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->extractFilters($request);

        $query = Category::query()
            ->with([
                'parent.translations',
                'translations',
            ])
            ->withCount([
                'children',
                'products',
            ]);

        if ($filters['search'] !== '') {
            $query->search($filters['search']);
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        if ($filters['parent'] === 'root') {
            $query->whereNull('parent_id');
        } elseif ($filters['parent'] !== '' && is_numeric($filters['parent'])) {
            $query->where('parent_id', (int) $filters['parent']);
        }

        $categories = $query
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $parents = $this->availableParents();

        return view('dashboard.categories.index', [
            'categories' => $categories,
            'parents' => $parents,
            'filters' => $filters,
            'canCreate' => $this->canCurrentUser('categories.create'),
            'canEdit' => $this->canCurrentUser('categories.edit'),
            'canDelete' => $this->canCurrentUser('categories.delete'),
            'canViewTrash' => $this->canCurrentUser('categories.view'),
        ]);
    }

    public function trash(): View
    {
        $categories = Category::onlyTrashed()
            ->with(['parent.translations', 'translations'])
            ->withCount(['children', 'products'])
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('dashboard.categories.trash', [
            'categories' => $categories,
            'canEdit' => $this->canCurrentUser('categories.edit'),
            'canDelete' => $this->canCurrentUser('categories.delete'),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.categories.create', [
            'parents' => $this->availableParents(),
            'form' => $this->formData(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $payload = $this->normalizedPayload($request);

        if (
            $payload['is_active']
            && $payload['parent_id']
            && ! $this->parentAllowsActiveChild($payload['parent_id'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'parent_id' => 'You cannot assign an active category under an inactive parent.',
                ]);
        }

        DB::transaction(function () use ($payload) {
            $category = Category::create([
                'parent_id' => $payload['parent_id'],
                'slug' => $payload['slug'],
                'is_active' => $payload['is_active'],
                'sort_order' => $payload['sort_order'],
                'meta_title' => $payload['meta_title'],
                'meta_description' => $payload['meta_description'],
            ]);

            foreach (['ar', 'en'] as $locale) {
                $category->translateOrNew($locale)->name = $payload[$locale]['name'] ?: null;
                $category->translateOrNew($locale)->description = $payload[$locale]['description'] ?: null;
            }

            $category->save();
        });

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function show(Category $category): View
    {
        $category->load([
            'parent.parent.translations',
            'parent.translations',
            'children.translations',
            'translations',
        ])->loadCount('products');

        return view('dashboard.categories.show', [
            'category' => $category,
            'breadcrumb' => $this->buildBreadcrumb($category),
            'canEdit' => $this->canCurrentUser('categories.edit'),
        ]);
    }

    public function edit(Category $category): View
    {
        $category->load('translations');

        return view('dashboard.categories.edit', [
            'category' => $category,
            'parents' => $this->availableParents($category->id),
            'form' => $this->formData($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $payload = $this->normalizedPayload($request);

        if ($this->wouldCreateCircularReference($category, $payload['parent_id'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'parent_id' => 'Invalid parent category selection.',
                ]);
        }

        if (
            $payload['is_active']
            && $payload['parent_id']
            && ! $this->parentAllowsActiveChild($payload['parent_id'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'parent_id' => 'You cannot activate this category while its parent is inactive.',
                ]);
        }

        DB::transaction(function () use ($payload, $category) {
            $category->update([
                'parent_id' => $payload['parent_id'],
                'slug' => $payload['slug'],
                'is_active' => $payload['is_active'],
                'sort_order' => $payload['sort_order'],
                'meta_title' => $payload['meta_title'],
                'meta_description' => $payload['meta_description'],
            ]);

            foreach (['ar', 'en'] as $locale) {
                $category->translateOrNew($locale)->name = $payload[$locale]['name'] ?: null;
                $category->translateOrNew($locale)->description = $payload[$locale]['description'] ?: null;
            }

            $category->save();

            if (! $category->is_active) {
                $category->children()->update(['is_active' => false]);
            }
        });

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->with('error', __('Cannot delete category because it has child categories.'));
        }

        if ($category->products()->exists()) {
            return back()->with('error', __('Cannot delete category because it has related products.'));
        }

        $category->delete();

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', __('Category moved to trash successfully.'));
    }

    public function restore(int $id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        if ($category->parent_id && ! $this->parentExistsAndActive($category->parent_id)) {
            return back()->with('error', __('Cannot restore category because its parent is missing or inactive.'));
        }

        $category->restore();

        return redirect()
            ->route('dashboard.categories.trash')
            ->with('success', __('Category restored successfully.'));
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $category = Category::onlyTrashed()
            ->withCount(['children', 'products'])
            ->findOrFail($id);

        if ($category->children_count > 0) {
            return back()->with('error', __('Cannot permanently delete category because it still has child categories.'));
        }

        if ($category->products_count > 0) {
            return back()->with('error', __('Cannot permanently delete category because it still has related products.'));
        }

        $category->translations()->delete();
        $category->forceDelete();

        return redirect()
            ->route('dashboard.categories.trash')
            ->with('success', __('Category permanently deleted.'));
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        $newStatus = ! $category->is_active;

        if ($newStatus && $category->parent_id && ! $this->parentAllowsActiveChild($category->parent_id)) {
            return back()->with('error', __('Cannot activate this category while parent category is inactive.'));
        }

        DB::transaction(function () use ($category, $newStatus) {
            $category->update([
                'is_active' => $newStatus,
            ]);

            if (! $newStatus) {
                $category->children()->update(['is_active' => false]);
            }
        });

        return back()->with('success', __('Category status updated successfully.'));
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:activate,deactivate,delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:categories,id'],
        ]);

        // Deleting needs the delete permission, the same as the single-item route.
        abort_if($request->input('action') === 'delete' && ! $request->user()->hasPermission('categories.delete'), 403);

        $ids = collect($request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return back()->with('error', __('No categories selected.'));
        }

        $categories = Category::whereIn('id', $ids)->get();

        if ($request->action === 'activate') {
            foreach ($categories as $category) {
                if (! $category->parent_id || $this->parentAllowsActiveChild($category->parent_id)) {
                    $category->update(['is_active' => true]);
                }
            }

            return back()->with('success', __('Selected categories activated successfully.'));
        }

        if ($request->action === 'deactivate') {
            foreach ($categories as $category) {
                $category->update(['is_active' => false]);
                $category->children()->update(['is_active' => false]);
            }

            return back()->with('success', __('Selected categories deactivated successfully.'));
        }

        if ($request->action === 'delete') {
            foreach ($categories as $category) {
                if (! $category->children()->exists() && ! $category->products()->exists()) {
                    $category->delete();
                }
            }

            return back()->with('success', __('Bulk delete completed.'));
        }

        return back()->with('error', __('Invalid bulk action.'));
    }

    private function extractFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->input('search', '')),
            'status' => (string) $request->input('status', ''),
            'parent' => (string) $request->input('parent', ''),
        ];
    }

    private function availableParents(?int $exceptId = null)
    {
        return Category::query()
            ->with('translations')
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->ordered()
            ->get();
    }

    private function formData(?Category $category = null): array
    {
        return [
            'ar' => [
                'name' => old('ar.name', $category?->translate('ar')?->name ?? ''),
                'description' => old('ar.description', $category?->translate('ar')?->description ?? ''),
            ],
            'en' => [
                'name' => old('en.name', $category?->translate('en')?->name ?? ''),
                'description' => old('en.description', $category?->translate('en')?->description ?? ''),
            ],
            'slug' => old('slug', $category->slug ?? ''),
            'parent_id' => old('parent_id', $category->parent_id ?? ''),
            'sort_order' => old('sort_order', $category->sort_order ?? 0),
            'meta_title' => old('meta_title', $category->meta_title ?? ''),
            'meta_description' => old('meta_description', $category->meta_description ?? ''),
            'is_active' => old('is_active', $category?->is_active ?? true),
        ];
    }

    private function normalizedPayload(Request $request): array
    {
        $arName = trim((string) data_get($request->all(), 'ar.name', ''));
        $enName = trim((string) data_get($request->all(), 'en.name', ''));

        $slug = trim((string) $request->input('slug', ''));
        $generatedFrom = $enName !== '' ? $enName : $arName;
        $slug = $slug !== '' ? $slug : Str::slug($generatedFrom);
        $slug = Str::slug(Str::lower(trim($slug)));

        return [
            'parent_id' => $request->filled('parent_id') ? (int) $request->input('parent_id') : null,
            'slug' => $slug,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) $request->input('sort_order', 0),
            'meta_title' => trim((string) $request->input('meta_title', '')),
            'meta_description' => trim((string) $request->input('meta_description', '')),
            'ar' => [
                'name' => $arName,
                'description' => trim((string) data_get($request->all(), 'ar.description', '')),
            ],
            'en' => [
                'name' => $enName,
                'description' => trim((string) data_get($request->all(), 'en.description', '')),
            ],
        ];
    }

    private function buildBreadcrumb(Category $category): array
    {
        $items = [];
        $current = $category;

        while ($current) {
            $items[] = [
                'id' => $current->id,
                'name' => $current->name ?: "Category #{$current->id}",
            ];

            $current = $current->parent;
        }

        return array_reverse($items);
    }

    private function wouldCreateCircularReference(Category $category, $parentId): bool
    {
        if (! $parentId) {
            return false;
        }

        if ((int) $parentId === (int) $category->id) {
            return true;
        }

        $parent = Category::find($parentId);

        while ($parent) {
            if ((int) $parent->id === (int) $category->id) {
                return true;
            }

            $parent = $parent->parent;
        }

        return false;
    }

    private function parentAllowsActiveChild(int $parentId): bool
    {
        return Category::query()
            ->where('id', $parentId)
            ->where('is_active', true)
            ->exists();
    }

    private function parentExistsAndActive(int $parentId): bool
    {
        return Category::query()
            ->where('id', $parentId)
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->exists();
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
