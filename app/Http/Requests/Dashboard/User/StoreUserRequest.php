<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:120'],
            'email'          => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone'          => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password'       => ['required', 'confirmed', Password::min(8)],
            'email_verified' => ['nullable', 'boolean'],
            'roles'          => ['nullable', 'array'],
            'roles.*'        => ['integer', 'exists:roles,id'],
        ];
    }
}
