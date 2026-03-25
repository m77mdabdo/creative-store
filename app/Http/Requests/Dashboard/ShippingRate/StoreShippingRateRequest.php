<?php

namespace App\Http\Requests\Dashboard\ShippingRate;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'shipping_zone_id' => ['required', 'integer', 'exists:shipping_zones,id'],
            'shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'min_weight' => ['nullable', 'numeric', 'min:0'],
            'max_weight' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'eta_min_days' => ['nullable', 'integer', 'min:0'],
            'eta_max_days' => ['nullable', 'integer', 'min:0'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'cash_on_delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
