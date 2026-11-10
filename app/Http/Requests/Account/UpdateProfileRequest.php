<?php

namespace App\Http\Requests\Account;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Propaganistas\LaravelPhone\Rules\Phone;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => trim((string) $this->input('name')),
            'email' => Str::lower(trim((string) $this->input('email', $this->user()?->email))),
            'phone' => trim((string) $this->input('phone')) ?: null,
        ]);
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'             => ['required', 'string', 'min:2', 'max:120'],
            'email'            => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'            => ['nullable', (new Phone())->country('SA')->international()],
            'avatar'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=64,min_height=64,max_width=4000,max_height=4000'],
            'remove_avatar'    => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $user = $this->user();

                // Changing the sign-in email needs the current password.
                if ($this->input('email') !== Str::lower($user->email)
                    && ! Hash::check((string) $this->input('current_password'), $user->password)) {
                    $validator->errors()->add('current_password', __('account.password_needed_for_email'));
                }

                $phone = $this->normalizedPhone();

                if ($phone && User::query()->whereKeyNot($user->id)->where('phone', $phone)->exists()) {
                    $validator->errors()->add('phone', __('account.phone_taken'));
                }
            },
        ];
    }

    /**
     * The phone number in E.164 (+9665XXXXXXXX), the format stored at sign-up.
     */
    public function normalizedPhone(): ?string
    {
        $phone = $this->input('phone');

        return $phone ? phone($phone, 'SA')->formatE164() : null;
    }

    public function attributes(): array
    {
        return [
            'name'             => __('account.full_name'),
            'email'            => __('account.email'),
            'phone'            => __('account.phone'),
            'avatar'           => __('account.photo'),
            'current_password' => __('account.current_password'),
        ];
    }
}
