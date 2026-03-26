<?php

namespace App\Http\Requests\Dashboard\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'q'          => ['nullable', 'string', 'max:150'],
            'city'       => ['nullable', 'string', 'max:100'],
            'is_default' => ['nullable', 'in:0,1'],
            'has_user'   => ['nullable', 'in:0,1'],
            'sort'       => ['nullable', 'in:latest,oldest,city_asc,city_desc,name_asc,name_desc'],
            'per_page'   => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }
}
