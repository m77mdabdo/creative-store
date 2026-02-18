<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\VerifyAccountMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $otp = (string) random_int(100000, 999999);

        // Store pending registration for 10 minutes
        Cache::put($this->cacheKey($request->email), [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ], now()->addMinutes(10));

        // Send OTP email
        Mail::to($request->email)->send(
            new VerifyAccountMail((int) $otp, $request->email)
        );

        return redirect()->route('email.verify', $request->email)
            ->with('success', 'We sent a verification code to your email.');
    }
    

    private function cacheKey(string $email): string
    {
        return 'pending_register:' . strtolower($email);
    }

}
