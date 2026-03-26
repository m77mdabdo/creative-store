<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserBulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'action'   => ['required', 'in:verify,unverify,delete'],
            'ids'      => ['required', 'array', 'min:1'],
            'ids.*'    => ['integer', 'exists:users,id'],
        ];
    }
}
