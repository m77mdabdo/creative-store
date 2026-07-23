<?php

namespace App\Http\Requests\Dashboard\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('owner')
            || auth()->user()->hasRole('admin')
            || auth()->user()->hasPermission('products.create')
        );
    }

public function rules(): array
{
    return [
        'category_id' => ['required', 'integer', 'exists:categories,id'],
        'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
        'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
        'base_price' => ['required', 'numeric', 'min:0'],
        'currency' => ['required', 'string', 'max:10'],
        'status' => ['required', 'string', 'in:active,draft,archived'],
        'has_variants' => ['nullable', 'boolean'],
        'tax_rate' => ['nullable', 'numeric', 'min:0'],
        'weight' => ['nullable', 'numeric', 'min:0'],
        'is_featured' => ['nullable', 'boolean'],
        'meta_title' => ['nullable', 'string', 'max:255'],
        'meta_description' => ['nullable', 'string', 'max:1000'],

        'attribute_ids' => ['nullable', 'array'],
        'attribute_ids.*' => ['integer', 'exists:attributes,id'],

        'translations' => ['required', 'array'],
        'translations.ar.name' => ['required', 'string', 'max:255'],
        'translations.ar.description' => ['nullable', 'string'],
        'translations.en.name' => ['nullable', 'string', 'max:255'],
        'translations.en.description' => ['nullable', 'string'],
    ];
}
}
