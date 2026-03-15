<?php

namespace App\Http\Requests\Dashboard\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
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
        return [
            'sku' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'values' => ['required', 'array', 'min:1'],
            'values.*.attribute_id' => ['required', 'integer', 'exists:attributes,id'],
            'values.*.attribute_value_id' => ['required', 'integer', 'exists:attribute_values,id'],
        ];
    }
}
