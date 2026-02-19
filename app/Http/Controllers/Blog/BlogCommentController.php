<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function store(Request $request, $blogId)
    {
        // Comments are only taken on posts readers can actually see.
        $blog = Blog::query()->published()->findOrFail($blogId);

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'comment' => ['required', 'string', 'max:5000'],
        ]);

        BlogComment::create([
            'blog_id' => $blog->id,
            'name'    => $data['name'],
            'email'   => $data['email'],
            'website' => $data['website'] ?? null,
            'comment' => $data['comment'],
        ]);

        return back()->with('success', __('Comment added successfully'));
    }
}
