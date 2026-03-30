<?php

namespace App\Http\Requests\Dashboard\ErrorLog;

use Illuminate\Foundation\Http\FormRequest;

class BulkErrorLogActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('audit.view');
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:error_logs,id'],
        ];
    }
}
