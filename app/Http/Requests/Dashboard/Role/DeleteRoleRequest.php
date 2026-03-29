<?php

namespace App\Http\Requests\Dashboard\Role;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('roles.delete') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
