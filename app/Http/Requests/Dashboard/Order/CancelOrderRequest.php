<?php

namespace App\Http\Requests\Dashboard\Order;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:1000'],
            'refund_if_paid' => ['nullable', 'boolean'],
            'refund_amount' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }
}
