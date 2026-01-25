<?php

namespace App\Http\Requests\Dashboard\Backup;

use Illuminate\Foundation\Http\FormRequest;

class IndexBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('backups.view');
    }

    public function rules(): array
    {
        return [
            'q'         => ['nullable', 'string', 'max:255'],
            'type'      => ['nullable', 'in:manual,scheduled'],
            'status'    => ['nullable', 'in:success,failed'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'  => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q' => is_string($this->q) ? trim($this->q) : $this->q,
        ]);
    }
}
