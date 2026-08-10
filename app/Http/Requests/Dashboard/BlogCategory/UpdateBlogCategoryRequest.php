<?php

namespace App\Http\Requests\Dashboard\BlogCategory;

use App\Models\BlogCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (
            $user->hasRole('owner') ||
            $user->hasRole('admin') ||
            $user->hasPermission('blog-categories.edit')
        );
    }

    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'name' => $name,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
        ]);
    }

    public function rules(): array
    {
        /** @var BlogCategory|null $blogCategory */
        $blogCategory = $this->route('blog_category');

        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'name_ar' => ['nullable', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'min:2',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_categories', 'slug')->ignore($blogCategory?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug format is invalid. Use lowercase letters, numbers, and hyphens only.',
        ];
    }
}
