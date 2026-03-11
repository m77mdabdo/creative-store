<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'street' => ['nullable', 'string', 'max:255'],
            'building_no' => ['nullable', 'string', 'max:50'],
            'apartment_no' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
