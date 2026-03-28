<?php

namespace App\Http\Requests\Dashboard\BlogCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (
            $user->hasRole('owner') ||
            $user->hasRole('admin') ||
            $user->hasPermission('blog-categories.create')
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
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'slug' => [
                'required',
                'string',
                'min:2',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_categories', 'slug'),
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
