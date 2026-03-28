<?php

namespace App\Http\Requests\Dashboard\BlogCategory;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (
            $user->hasRole('owner') ||
            $user->hasRole('admin') ||
            $user->hasPermission('blog-categories.delete')
        );
    }

    public function rules(): array
    {
        return [];
    }
}
