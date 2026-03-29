<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserBulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('users.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'ids'      => ['required', 'array', 'min:1'],
            'ids.*'    => ['integer', 'exists:users,id'],
            'action'   => ['required', 'in:verify,unverify,delete'],
        ];
    }
}
