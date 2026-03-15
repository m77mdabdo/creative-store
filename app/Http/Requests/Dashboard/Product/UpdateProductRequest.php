<?php

namespace App\Http\Requests\Dashboard\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('owner')
            || auth()->user()->hasRole('admin')
            || auth()->user()->hasPermission('products.edit')
        );
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'base_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'status' => ['required', 'string', 'in:active,inactive,draft'],
            'has_variants' => ['nullable', 'boolean'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],

            'translations' => ['required', 'array'],
            'translations.ar.name' => ['required', 'string', 'max:255'],
            'translations.ar.description' => ['nullable', 'string'],
            'translations.en.name' => ['nullable', 'string', 'max:255'],
            'translations.en.description' => ['nullable', 'string'],
        ];
    }
}
