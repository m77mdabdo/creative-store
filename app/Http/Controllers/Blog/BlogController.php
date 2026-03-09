<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
public function index(Request $request)
{
    $query = Blog::with('category')->where('status', true);

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

    $blogs = $query->latest('published_at')->paginate(6)->withQueryString();

    $categories = BlogCategory::withCount(['blogs' => function ($q) {
        $q->where('status', true);
    }])->get();

    $featuredBlogs = Blog::where('status', true)
        ->where('is_featured', true)
        ->latest('published_at')
        ->take(3)
        ->get();

    $latestBlogs = Blog::where('status', true)
        ->latest('published_at')
        ->take(5)
        ->get();

    return view('site.blog.blog', compact('blogs', 'categories', 'featuredBlogs', 'latestBlogs'));
}

    public function show($slug)
    {
        $blog = Blog::with('category', 'comments')
            ->where('status', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $blog->increment('views');

        $categories = BlogCategory::withCount(['blogs' => function ($q) {
            $q->where('status', true);
        }])->get();

        $latestBlogs = Blog::where('status', true)
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        $relatedBlogs = Blog::with('category')
            ->where('status', true)
            ->where('id', '!=', $blog->id)
            ->where('blog_category_id', $blog->blog_category_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('site.blog.blog-detail', compact(
            'blog',
            'categories',
            'latestBlogs',
            'relatedBlogs'
        ));
    }
}
