<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /**
     * Normalise Arabic-Indic and Persian digits so codes typed on an Arabic
     * keyboard validate like Latin ones.
     */
    protected function prepareForValidation(): void
    {
        $otp = $this->input('otp');

        if (is_array($otp)) {
            $this->merge([
                'otp' => array_map(
                    fn ($digit) => strtr((string) $digit, [
                        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
                        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
                        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
                    ]),
                    $otp,
                ),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'otp' => ['required', 'array', 'size:6'],
            'otp.*' => ['required', 'digits:1'],
        ];
    }
}
