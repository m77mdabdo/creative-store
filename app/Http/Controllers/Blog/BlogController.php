<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $fallbackBlogImage = asset('cozastore/images/blog-04.jpg');

        $query = Blog::with('category')
            ->where('status', true);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhere('author', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $blogs = $query->latest('published_at')
            ->paginate(6)
            ->withQueryString();

        $blogs->getCollection()->transform(function ($blog) use ($fallbackBlogImage) {
            $blog->image_url = $this->resolveBlogImageUrl($blog->image, $fallbackBlogImage);
            return $blog;
        });

        $categories = BlogCategory::withCount(['blogs' => function ($q) {
            $q->where('status', true);
        }])->get();

        $featuredBlogs = Blog::where('status', true)
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(function ($blog) use ($fallbackBlogImage) {
                $blog->image_url = $this->resolveBlogImageUrl($blog->image, $fallbackBlogImage);
                return $blog;
            });

        $latestBlogs = Blog::where('status', true)
            ->latest('published_at')
            ->take(5)
            ->get()
            ->map(function ($blog) use ($fallbackBlogImage) {
                $blog->image_url = $this->resolveBlogImageUrl($blog->image, $fallbackBlogImage);
                return $blog;
            });

        return view('site.blog.blog', compact(
            'blogs',
            'categories',
            'featuredBlogs',
            'latestBlogs'
        ));
    }

    public function show($slug)
    {
        $fallbackBlogImage = asset('cozastore/images/blog-04.jpg');

        $blog = Blog::with('category', 'comments')
            ->where('status', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $blog->increment('views');

        $blog->image_url = $this->resolveBlogImageUrl($blog->image, $fallbackBlogImage);

        $categories = BlogCategory::withCount(['blogs' => function ($q) {
            $q->where('status', true);
        }])->get();

        $latestBlogs = Blog::where('status', true)
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->take(5)
            ->get()
            ->map(function ($item) use ($fallbackBlogImage) {
                $item->image_url = $this->resolveBlogImageUrl($item->image, $fallbackBlogImage);
                return $item;
            });

        $relatedBlogs = Blog::with('category')
            ->where('status', true)
            ->where('id', '!=', $blog->id)
            ->where('blog_category_id', $blog->blog_category_id)
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(function ($item) use ($fallbackBlogImage) {
                $item->image_url = $this->resolveBlogImageUrl($item->image, $fallbackBlogImage);
                return $item;
            });

        return view('site.blog.blog-detail', compact(
            'blog',
            'categories',
            'latestBlogs',
            'relatedBlogs'
        ));
    }

    protected function resolveBlogImageUrl(?string $path, string $fallback): string
    {
        if (!$path) {
            return $fallback;
        }

        $path = trim($path);

        if ($path === '') {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return Storage::url($path);
    }
}
