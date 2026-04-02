<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends StoreAddressRequest
{
    /**
     * Authorization
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Custom validation messages (Arabic / English)
     */
    public function messages(): array
    {
        return [

            'full_name.required' => __('validation.full_name_required'),
            'phone.required' => __('validation.phone_required'),
            'country.required' => __('validation.country_required'),
            'city.required' => __('validation.city_required'),

            'lat.numeric' => __('validation.latitude_numeric'),
            'lng.numeric' => __('validation.longitude_numeric'),

        ];
    }

    /**
     * Attribute names (for clean validation output)
     */
    public function attributes(): array
    {
        return [

            'full_name' => __('site.full_name'),
            'phone' => __('site.phone'),
            'country' => __('site.country'),
            'city' => __('site.city'),
            'district' => __('site.district'),
            'street' => __('site.street'),
            'building_no' => __('site.building_no'),
            'apartment_no' => __('site.apartment_no'),
            'postal_code' => __('site.postal_code'),
            'lat' => __('site.latitude'),
            'lng' => __('site.longitude'),

        ];
    }
}
