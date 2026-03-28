<?php

namespace App\Http\Requests\Dashboard\Page;

use Illuminate\Foundation\Http\FormRequest;

class DeletePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (
            $user->hasRole('owner') ||
            $user->hasRole('admin') ||
            $user->hasPermission('pages.delete')
        );
    }

    public function rules(): array
    {
        return [];
    }
}
