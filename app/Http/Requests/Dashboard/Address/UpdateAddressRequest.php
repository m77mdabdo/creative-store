<?php

namespace App\Http\Requests\Dashboard\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'label'        => ['nullable', 'string', 'max:100'],
            'full_name'    => ['required', 'string', 'max:150'],
            'phone'        => ['required', 'string', 'max:30'],
            'country'      => ['required', 'string', 'max:100'],
            'city'         => ['required', 'string', 'max:100'],
            'district'     => ['nullable', 'string', 'max:100'],
            'street'       => ['required', 'string', 'max:150'],
            'building_no'  => ['nullable', 'string', 'max:50'],
            'apartment_no' => ['nullable', 'string', 'max:50'],
            'postal_code'  => ['nullable', 'string', 'max:20'],
            'lat'          => ['nullable', 'numeric', 'between:-90,90'],
            'lng'          => ['nullable', 'numeric', 'between:-180,180'],
            'notes'        => ['nullable', 'string'],
            'is_default'   => ['nullable', 'boolean'],
        ];
    }
}
