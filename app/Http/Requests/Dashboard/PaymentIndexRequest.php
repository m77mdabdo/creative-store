<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'q'          => ['nullable', 'string', 'max:150'],
            'status'     => ['nullable', Rule::in(Payment::statuses())],
            'gateway'    => ['nullable', 'string', 'max:50'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date', 'after_or_equal:date_from'],
            'amount_min' => ['nullable', 'numeric', 'min:0'],
            'amount_max' => ['nullable', 'numeric', 'min:0', 'gte:amount_min'],
            'sort'       => ['nullable', Rule::in(['newest', 'oldest', 'amount_asc', 'amount_desc'])],
            'per_page'   => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }
}
