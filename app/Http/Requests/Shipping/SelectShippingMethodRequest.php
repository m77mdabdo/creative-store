<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class SelectShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'shipping_rate_id' => ['required', 'exists:shipping_rates,id'],
        ];
    }
}
