<?php

namespace App\Http\Requests\Dashboard\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
            'shipping_status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
