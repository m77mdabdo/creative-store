<?php

namespace App\Http\Requests\Dashboard\AuditLog;

use Illuminate\Foundation\Http\FormRequest;

class IndexAuditLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('audit.view');
    }

    public function rules(): array
    {
        return [
            'q'              => ['nullable', 'string', 'max:255'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
            'action'         => ['nullable', 'string', 'max:255'],
            'auditable_type' => ['nullable', 'string', 'max:255'],
            'auditable_id'   => ['nullable', 'integer'],
            'ip_address'     => ['nullable', 'string', 'max:45'],
            'date_from'      => ['nullable', 'date'],
            'date_to'        => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'       => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q'              => is_string($this->q) ? trim($this->q) : $this->q,
            'action'         => is_string($this->action) ? trim($this->action) : $this->action,
            'auditable_type' => is_string($this->auditable_type) ? trim($this->auditable_type) : $this->auditable_type,
            'ip_address'     => is_string($this->ip_address) ? trim($this->ip_address) : $this->ip_address,
        ]);
    }
}
