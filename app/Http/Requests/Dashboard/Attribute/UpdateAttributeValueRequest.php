<?php

namespace App\Http\Requests\Dashboard\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('owner')
            || auth()->user()->hasRole('admin')
            || auth()->user()->hasPermission('attributes.edit')
        );
    }

    public function rules(): array
    {
        return [
            'value' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
