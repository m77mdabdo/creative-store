<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('users.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'q'              => ['nullable', 'string', 'max:255'],
            'role'           => ['nullable', 'string', 'max:100'],
            'type'           => ['nullable', 'in:owner,admin,employee,customer'],
            'email_verified' => ['nullable', 'in:0,1'],
            'has_phone'      => ['nullable', 'in:0,1'],
            'is_active'      => ['nullable', 'in:0,1'],
            'sort'           => ['nullable', 'in:latest,oldest,name_asc,name_desc,email_asc,email_desc'],
            'per_page'       => ['nullable', 'integer', 'min:15', 'max:100'],
        ];
    }
}
