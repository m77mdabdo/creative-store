<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('users.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone'                => ['nullable', 'string', 'max:50'],
            'password'             => ['required', 'string', 'min:8', 'confirmed'],
            'email_verified'       => ['nullable', 'boolean'],
            'is_active'            => ['nullable', 'boolean'],
            'roles'                => ['nullable', 'array'],
            'roles.*'              => ['integer', Rule::exists('roles', 'id')],
            'direct_permissions'   => ['nullable', 'array'],
            'direct_permissions.*' => ['string', Rule::exists('permissions', 'code')],
            'denied_permissions'   => ['nullable', 'array'],
            'denied_permissions.*' => ['string', Rule::exists('permissions', 'code')],
        ];
    }
}
