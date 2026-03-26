<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'q'              => ['nullable', 'string', 'max:150'],
            'role'           => ['nullable', 'string', 'max:100'],
            'email_verified' => ['nullable', 'in:0,1'],
            'has_phone'      => ['nullable', 'in:0,1'],
            'sort'           => ['nullable', 'in:latest,oldest,name_asc,name_desc,email_asc,email_desc'],
            'per_page'       => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }
}
