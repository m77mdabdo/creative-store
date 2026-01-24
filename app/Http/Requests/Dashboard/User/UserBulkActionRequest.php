<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserBulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $needed = $this->input('action') === 'delete' ? 'users.delete' : 'users.edit';

        return $this->user()?->hasPermission($needed) ?? false;
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
