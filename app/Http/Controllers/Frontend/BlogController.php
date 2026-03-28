<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function show(string $slug): View
    {
        $blog = Blog::query()
            ->where('slug', $slug)
            ->where('status', true)
            ->with(['category', 'comments'])
            ->firstOrFail();

        // زيادة عدد المشاهدات
        $blog->increment('views');

        return view('frontend.blog.show', [
            'blog' => $blog,
        ]);
    }
}
