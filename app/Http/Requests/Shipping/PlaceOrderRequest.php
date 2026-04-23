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
            'address_id' => ['required', 'integer'],
            'shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'shipping_rate_id' => ['required', 'integer', 'exists:shipping_rates,id'],
            'payment_method' => ['required', 'in:moyasar'],
        ];
    }
}
