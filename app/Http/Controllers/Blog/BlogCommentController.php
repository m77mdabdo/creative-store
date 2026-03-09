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
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'comment' => 'required'
        ]);

        BlogComment::create([
            'blog_id' => $blogId,
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'comment' => $request->comment
        ]);

        return back()->with('success','Comment added successfully');
    }
}
