<?php

namespace App\Http\Requests\Dashboard\Shipment;

use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'carrier' => ['nullable', 'string', 'max:150'],
            'tracking_number' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(Shipment::statuses())],
            'current_location' => ['nullable', 'string', 'max:255'],
            'carrier_status' => ['nullable', 'string', 'max:255'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
