<?php

namespace App\Http\Requests\Dashboard\Wishlist;

use Illuminate\Foundation\Http\FormRequest;

class WishlistIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'q'              => ['nullable', 'string', 'max:150'],
            'product_id'     => ['nullable', 'integer', 'exists:products,id'],
            'product_status' => ['nullable', 'in:active,inactive'],
            'min_items'      => ['nullable', 'integer', 'min:0'],
            'date_from'      => ['nullable', 'date'],
            'date_to'        => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort'           => ['nullable', 'in:latest,oldest,items_asc,items_desc,user_name_asc,user_name_desc'],
            'per_page'       => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }
}
