<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Category\StoreCategoryRequest;
use App\Http\Requests\Dashboard\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()
            ->with(['parent.translations', 'translations'])
            ->withCount(['children', 'products']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhereHas('translations', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('parent')) {
            if ($request->parent === 'root') {
                $query->whereNull('parent_id');
            } elseif (is_numeric($request->parent)) {
                $query->where('parent_id', $request->parent);
            }
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $parents = Category::with('translations')
            ->root()
            ->ordered()
            ->get();

        return view('dashboard.categories.index', compact('categories', 'parents'));
    }

    public function create()
    {
        $parents = Category::with('translations')
            ->ordered()
            ->get();

        return view('dashboard.categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request)
    {
        DB::transaction(function () use ($request) {
            $category = Category::create([
                'parent_id' => $request->parent_id,
                'slug' => $request->slug,
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $request->sort_order ?? 0,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]);

            foreach ($request->translations as $locale => $data) {
                if (!empty($data['name']) || !empty($data['description'])) {
                    $category->translations()->create([
                        'locale' => $locale,
                        'name' => $data['name'] ?? null,
                        'description' => $data['description'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $category->load([
            'parent.translations',
            'children.translations',
            'translations',
            'products',
        ]);

        return view('dashboard.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $category->load('translations');

        $parents = Category::with('translations')
            ->where('id', '!=', $category->id)
            ->ordered()
            ->get();

        return view('dashboard.categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if ($this->wouldCreateCircularReference($category, $request->parent_id)) {
            return back()
                ->withInput()
                ->withErrors(['parent_id' => 'Invalid parent category selection.']);
        }

        DB::transaction(function () use ($request, $category) {
            $category->update([
                'parent_id' => $request->parent_id,
                'slug' => $request->slug,
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $request->sort_order ?? 0,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]);

            foreach ($request->translations as $locale => $data) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $data['name'] ?? null,
                        'description' => $data['description'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category because it has child categories.');
        }

        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category because it has related products.');
        }

        $category->delete();

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return back()->with('success', 'Category status updated successfully.');
    }

    private function wouldCreateCircularReference(Category $category, $parentId): bool
    {
        if (!$parentId) {
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
}
