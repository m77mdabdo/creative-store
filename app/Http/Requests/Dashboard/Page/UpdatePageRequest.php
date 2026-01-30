<?php

namespace App\Http\Requests\Dashboard\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => strtolower(trim((string) $this->slug)),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $pageId = $this->route('page')?->id;

        return [
            'slug' => [
                'required',
                'string',
                'max:150',
                'alpha_dash',
                Rule::unique('pages', 'slug')->ignore($pageId),
            ],
            'is_active' => ['nullable', 'boolean'],

            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],

            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],

            'hero_title_ar' => ['nullable', 'string', 'max:255'],
            'hero_title_en' => ['nullable', 'string', 'max:255'],

            'story_title_ar' => ['nullable', 'string', 'max:255'],
            'story_title_en' => ['nullable', 'string', 'max:255'],
            'story_text_1_ar' => ['nullable', 'string'],
            'story_text_1_en' => ['nullable', 'string'],
            'story_text_2_ar' => ['nullable', 'string'],
            'story_text_2_en' => ['nullable', 'string'],
            'story_text_3_ar' => ['nullable', 'string'],
            'story_text_3_en' => ['nullable', 'string'],
            'mission_title_ar' => ['nullable', 'string', 'max:255'],
            'mission_title_en' => ['nullable', 'string', 'max:255'],
            'mission_text_ar' => ['nullable', 'string'],
            'mission_text_en' => ['nullable', 'string'],
            'quote_text_ar' => ['nullable', 'string'],
            'quote_text_en' => ['nullable', 'string'],
            'quote_author_ar' => ['nullable', 'string', 'max:255'],
            'quote_author_en' => ['nullable', 'string', 'max:255'],

            'image_1_ar' => ['nullable', 'string', 'max:255', 'regex:/^(images\/|pages\/|https?:\/\/)\S+$/', 'not_regex:/\.\./'],
            'image_1_en' => ['nullable', 'string', 'max:255', 'regex:/^(images\/|pages\/|https?:\/\/)\S+$/', 'not_regex:/\.\./'],
            'image_2_ar' => ['nullable', 'string', 'max:255', 'regex:/^(images\/|pages\/|https?:\/\/)\S+$/', 'not_regex:/\.\./'],
            'image_2_en' => ['nullable', 'string', 'max:255', 'regex:/^(images\/|pages\/|https?:\/\/)\S+$/', 'not_regex:/\.\./'],

            'image_1_ar_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_1_en_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_2_ar_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_2_en_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'form_title_ar' => ['nullable', 'string', 'max:255'],
            'form_title_en' => ['nullable', 'string', 'max:255'],
            'address_title_ar' => ['nullable', 'string', 'max:255'],
            'address_title_en' => ['nullable', 'string', 'max:255'],
            'address_text_ar' => ['nullable', 'string', 'max:1000'],
            'address_text_en' => ['nullable', 'string', 'max:1000'],
            'phone_title_ar' => ['nullable', 'string', 'max:255'],
            'phone_title_en' => ['nullable', 'string', 'max:255'],
            'phone_text_ar' => ['nullable', 'string', 'max:255'],
            'phone_text_en' => ['nullable', 'string', 'max:255'],
            'email_title_ar' => ['nullable', 'string', 'max:255'],
            'email_title_en' => ['nullable', 'string', 'max:255'],
            'email_text_ar' => ['nullable', 'string', 'max:255'],
            'email_text_en' => ['nullable', 'string', 'max:255'],
            'map_x_ar' => ['nullable', 'numeric'],
            'map_x_en' => ['nullable', 'numeric'],
            'map_y_ar' => ['nullable', 'numeric'],
            'map_y_en' => ['nullable', 'numeric'],
            'map_zoom_ar' => ['nullable', 'integer', 'min:1', 'max:20'],
            'map_zoom_en' => ['nullable', 'integer', 'min:1', 'max:20'],
        ];
    }
}
