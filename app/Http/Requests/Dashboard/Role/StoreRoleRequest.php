<?php

namespace App\Http\Requests\Dashboard\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('roles.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:100', Rule::unique('roles', 'name')],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'code')],
        ];
    }
}
