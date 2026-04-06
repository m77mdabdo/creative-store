<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Blog\DeleteBlogRequest;
use App\Http\Requests\Dashboard\Blog\StoreBlogRequest;
use App\Http\Requests\Dashboard\Blog\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'featured' => ['nullable', 'in:0,1'],
            'status' => ['nullable', 'in:0,1'],
            'sort' => ['nullable', 'in:newest,oldest,title_asc,title_desc,most_viewed'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $perPage = (int) ($filters['per_page'] ?? 15);
        $sort = $filters['sort'] ?? 'newest';

        $query = Blog::query()
            ->with(['category:id,name,slug'])
            ->withCount('comments')
            ->search($filters['q'] ?? null);

        if (isset($filters['category_id']) && $filters['category_id'] !== null && $filters['category_id'] !== '') {
            $query->where('blog_category_id', (int) $filters['category_id']);
        }

        if (($filters['featured'] ?? '') !== '') {
            $query->where('is_featured', (bool) $filters['featured']);
        }

        if (($filters['status'] ?? '') !== '') {
            $query->where('status', (bool) $filters['status']);
        }

        $query = match ($sort) {
            'oldest' => $query->oldest('id'),
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            'most_viewed' => $query->orderByDesc('views')->orderByDesc('id'),
            default => $query->latest('id'),
        };

        $blogs = $query->paginate($perPage)->withQueryString();

        $categories = BlogCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('dashboard.blogs.index', [
            'blogs' => $blogs,
            'categories' => $categories,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'category_id' => $filters['category_id'] ?? '',
                'featured' => $filters['featured'] ?? '',
                'status' => $filters['status'] ?? '',
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): View
    {
        $categories = BlogCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('dashboard.blogs.create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['views'] = 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs');
        }

        $blog = Blog::create($data);

        return redirect()
            ->route('dashboard.blogs.index')
            ->with('success', "Blog [{$blog->title}] created successfully.");
    }

    public function show(Blog $blog): View
    {
        $blog->load([
            'category:id,name,slug',
            'comments' => fn ($query) => $query->latest('id'),
        ]);

        return view('dashboard.blogs.show', [
            'blog' => $blog,
        ]);
    }

    public function edit(Blog $blog): View
    {
        $categories = BlogCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('dashboard.blogs.edit', [
            'blog' => $blog,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateBlogRequest $request, Blog $blog): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($blog->image && Storage::exists($blog->image)) {
                Storage::delete($blog->image);
            }

            $data['image'] = $request->file('image')->store('blogs');
        }

        $blog->update($data);

        return redirect()
            ->route('dashboard.blogs.index')
            ->with('success', "Blog [{$blog->title}] updated successfully.");
    }

    public function toggleStatus(Blog $blog): RedirectResponse
    {
        $blog->update([
            'status' => ! $blog->status,
        ]);

        return redirect()
            ->route('dashboard.blogs.index')
            ->with('success', 'Blog status updated successfully.');
    }

    public function toggleFeatured(Blog $blog): RedirectResponse
    {
        $blog->update([
            'is_featured' => ! $blog->is_featured,
        ]);

        return redirect()
            ->route('dashboard.blogs.index')
            ->with('success', 'Blog featured flag updated successfully.');
    }

    public function destroy(DeleteBlogRequest $request, Blog $blog): RedirectResponse
    {
        if ($blog->comments()->exists()) {
            return redirect()
                ->route('dashboard.blogs.index')
                ->with('error', 'Cannot delete this blog because it has comments.');
        }

        if ($blog->image && Storage::exists($blog->image)) {
            Storage::delete($blog->image);
        }

        $title = $blog->title;
        $blog->delete();

        return redirect()
            ->route('dashboard.blogs.index')
            ->with('success', "Blog [{$title}] deleted successfully.");
    }
}
