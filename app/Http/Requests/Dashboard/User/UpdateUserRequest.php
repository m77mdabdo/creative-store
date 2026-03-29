<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('users.edit') ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'                => ['nullable', 'string', 'max:50'],
            'password'             => ['nullable', 'string', 'min:8', 'confirmed'],
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
