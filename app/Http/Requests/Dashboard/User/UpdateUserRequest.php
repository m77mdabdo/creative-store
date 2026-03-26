<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'name'           => ['required', 'string', 'max:120'],
            'email'          => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'phone'          => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($userId)],
            'password'       => ['nullable', 'confirmed', Password::min(8)],
            'email_verified' => ['nullable', 'boolean'],
            'roles'          => ['nullable', 'array'],
            'roles.*'        => ['integer', 'exists:roles,id'],
        ];
    }
}
