<?php

namespace App\Http\Requests\Dashboard\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->hasPermission('categories.create') ||
            auth()->user()->hasRole('owner') ||
            auth()->user()->hasRole('admin')
        );
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:categories,slug'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],

            'translations' => ['required', 'array'],
            'translations.ar.name' => ['required', 'string', 'max:255'],
            'translations.ar.description' => ['nullable', 'string'],
            'translations.en.name' => ['nullable', 'string', 'max:255'],
            'translations.en.description' => ['nullable', 'string'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->sort_order ?? 0,
        ]);
    }

    public function messages(): array
    {
        return [
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'This slug is already taken.',
            'translations.ar.name.required' => 'Arabic category name is required.',
        ];
    }
}
