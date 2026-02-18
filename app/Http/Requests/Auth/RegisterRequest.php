<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;
use Illuminate\Support\Facades\Cache;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                new Phone(),
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:6',
                'string',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email = strtolower((string) $this->input('email'));
            if (!$email) return;

            $key = 'pending_register:' . $email;

            if (Cache::has($key)) {
                $validator->errors()->add('email', 'هذا البريد لديه تسجيل مُعلّق. تحقق من OTP أو انتظر انتهاء الصلاحية.');
            }
        });
    }

    protected function passedValidation()
    {
        if ($this->phone) {
            $this->merge([
                'phone' => phone($this->phone)->formatE164(),
            ]);
        }
    }
}
