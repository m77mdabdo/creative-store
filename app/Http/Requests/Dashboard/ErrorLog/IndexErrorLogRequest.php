<?php

namespace App\Http\Requests\Dashboard\ErrorLog;

use Illuminate\Foundation\Http\FormRequest;

class IndexErrorLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('audit.view');
    }

    public function rules(): array
    {
        return [
            'q'            => ['nullable', 'string', 'max:255'],
            'level'        => ['nullable', 'string', 'max:20'],
            'service'      => ['nullable', 'string', 'max:80'],
            'resolved'     => ['nullable', 'in:yes,no'],
            'search_trace' => ['nullable', 'in:0,1'],
            'date_from'    => ['nullable', 'date'],
            'date_to'      => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'     => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q'            => is_string($this->q) ? trim($this->q) : $this->q,
            'level'        => is_string($this->level) ? trim($this->level) : $this->level,
            'service'      => is_string($this->service) ? trim($this->service) : $this->service,
            'search_trace' => $this->search_trace ?? '0',
        ]);
    }
}
