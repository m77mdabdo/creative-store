<?php

namespace App\Http\Requests\Dashboard\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->hasPermission('categories.edit') ||
            auth()->user()->hasRole('owner') ||
            auth()->user()->hasRole('admin')
        );
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id ?? null;

        return [
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                Rule::notIn([$categoryId]),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],

            'ar' => ['required', 'array'],
            'ar.name' => ['required', 'string', 'max:255'],
            'ar.description' => ['nullable', 'string'],

            'en' => ['required', 'array'],
            'en.name' => ['required', 'string', 'max:255'],
            'en.description' => ['nullable', 'string'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => (int) ($this->input('sort_order', 0)),
        ]);
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This slug is already taken.',
            'ar.name.required' => 'Arabic category name is required.',
            'en.name.required' => 'English category name is required.',
        ];
    }
}
