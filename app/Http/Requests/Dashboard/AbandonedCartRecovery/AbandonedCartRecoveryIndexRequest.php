<?php

namespace App\Http\Requests\Dashboard\AbandonedCartRecovery;

use Illuminate\Foundation\Http\FormRequest;

class AbandonedCartRecoveryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'q'         => ['nullable', 'string', 'max:150'],
            'status'    => ['nullable', 'in:pending,sent,failed,recovered,expired'],
            'channel'   => ['nullable', 'in:email'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort'      => ['nullable', 'in:latest,oldest,expires_asc,expires_desc'],
            'per_page'  => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }
}
