<?php

namespace App\Http\Requests\Dashboard\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && (
            $user->hasRole('owner') ||
            $user->hasRole('admin') ||
            $user->hasPermission('pages.create')
        );
    }

    protected function prepareForValidation(): void
    {
        $slug = trim((string) $this->input('slug'));
        $titleEn = trim((string) $this->input('title_en'));
        $titleAr = trim((string) $this->input('title_ar'));

        $this->merge([
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($titleEn ?: $titleAr),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug'),
            ],
            'is_active' => ['nullable', 'boolean'],

            'title_ar' => ['required', 'string', 'max:255'],
            'content_ar' => ['required', 'string', 'min:3'],

            'title_en' => ['required', 'string', 'max:255'],
            'content_en' => ['required', 'string', 'min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'Slug must contain lowercase letters, numbers, and hyphens only.',
        ];
    }
}
