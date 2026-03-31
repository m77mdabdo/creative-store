<?php

namespace App\Http\Requests\Dashboard\Backup;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('backup.delete');
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:system_backups,id'],
        ];
    }
}
