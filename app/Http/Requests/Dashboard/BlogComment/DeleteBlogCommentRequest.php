<?php

namespace App\Http\Requests\Dashboard\BlogComment;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBlogCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (
            $user->hasRole('owner') ||
            $user->hasRole('admin') ||
            $user->hasPermission('blog-comments.delete')
        );
    }

    public function rules(): array
    {
        return [];
    }
}
