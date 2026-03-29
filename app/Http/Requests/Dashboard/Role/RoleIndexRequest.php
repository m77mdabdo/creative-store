<?php

namespace App\Http\Requests\Dashboard\Role;

use Illuminate\Foundation\Http\FormRequest;

class RoleIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('roles.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'q'        => ['nullable', 'string', 'max:255'],
            'sort'     => ['nullable', 'in:name_asc,name_desc,users_count_desc,users_count_asc,latest,oldest'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }
}
