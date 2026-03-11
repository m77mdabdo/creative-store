<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'exists:addresses,id'],
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'shipping_rate_id' => ['required', 'exists:shipping_rates,id'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ];
    }
}
