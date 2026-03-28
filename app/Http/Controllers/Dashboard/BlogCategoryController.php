<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BlogCategory\DeleteBlogCategoryRequest;
use App\Http\Requests\Dashboard\BlogCategory\StoreBlogCategoryRequest;
use App\Http\Requests\Dashboard\BlogCategory\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q'        => ['nullable', 'string', 'max:150'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
            'sort'     => ['nullable', 'in:newest,oldest,name_asc,name_desc'],
        ]);

        $perPage = (int) ($filters['per_page'] ?? 15);
        $sort    = $filters['sort'] ?? 'newest';

        $query = BlogCategory::query()
            ->withCount('blogs')
            ->search($filters['q'] ?? null);

        $query = match ($sort) {
            'oldest'    => $query->oldest('id'),
            'name_asc'  => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default     => $query->latest('id'),
        };

        $categories = $query->paginate($perPage)->withQueryString();

        return view('dashboard.blog-categories.index', [
            'categories' => $categories,
            'filters'    => [
                'q'        => $filters['q'] ?? '',
                'per_page' => $perPage,
                'sort'     => $sort,
            ],
        ]);
    }

    public function create(): View
    {
        return view('dashboard.blog-categories.create');
    }

    public function store(StoreBlogCategoryRequest $request): RedirectResponse
    {
        $category = BlogCategory::create($request->validated());

        return redirect()
            ->route('dashboard.blog-categories.index')
            ->with('success', "Blog category [{$category->name}] created successfully.");
    }

    public function edit(BlogCategory $blog_category): View
    {
        return view('dashboard.blog-categories.edit', [
            'category' => $blog_category,
        ]);
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blog_category): RedirectResponse
    {
        $blog_category->update($request->validated());

        return redirect()
            ->route('dashboard.blog-categories.index')
            ->with('success', "Blog category [{$blog_category->name}] updated successfully.");
    }

    public function destroy(DeleteBlogCategoryRequest $request, BlogCategory $blog_category): RedirectResponse
    {
        $blogsCount = $blog_category->blogs()->count();

        if ($blogsCount > 0) {
            return redirect()
                ->route('dashboard.blog-categories.index')
                ->with('error', "Cannot delete category [{$blog_category->name}] because it contains {$blogsCount} blog(s).");
        }

        $name = $blog_category->name;
        $blog_category->delete();

        return redirect()
            ->route('dashboard.blog-categories.index')
            ->with('success', "Blog category [{$name}] deleted successfully.");
    }
}
