<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BlogComment\DeleteBlogCommentRequest;
use App\Models\BlogComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogCommentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'blog_id' => ['nullable', 'integer', 'exists:blogs,id'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
            'sort' => ['nullable', 'in:newest,oldest,name_asc,name_desc'],
        ]);

        $perPage = (int) ($filters['per_page'] ?? 15);
        $sort = $filters['sort'] ?? 'newest';

        $query = BlogComment::query()
            ->with(['blog:id,title,slug'])
            ->search($filters['q'] ?? null);

        if (!empty($filters['blog_id'])) {
            $query->where('blog_id', (int) $filters['blog_id']);
        }

        $query = match ($sort) {
            'oldest' => $query->oldest('id'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest('id'),
        };

        $comments = $query->paginate($perPage)->withQueryString();

        $blogs = \App\Models\Blog::query()
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('dashboard.blog-comments.index', [
            'comments' => $comments,
            'blogs' => $blogs,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'blog_id' => $filters['blog_id'] ?? '',
                'per_page' => $perPage,
                'sort' => $sort,
            ],
        ]);
    }

    public function show(BlogComment $blogComment): View
    {
        $blogComment->load(['blog:id,title,slug']);

        return view('dashboard.blog-comments.show', [
            'comment' => $blogComment,
        ]);
    }

    public function destroy(DeleteBlogCommentRequest $request, BlogComment $blogComment): RedirectResponse
    {
        $blogComment->delete();

        return redirect()
            ->route('dashboard.blog-comments.index')
            ->with('success', __('Comment deleted successfully.'));
    }
}
