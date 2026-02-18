<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyAccountRequest;

     use App\Mail\AccountVerifiedMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class VerifyAccountController extends Controller
{
    public function __invoke(VerifyAccountRequest $request)
    {
        $key = 'pending_register:' . strtolower($request->email);
        $pending = Cache::get($key);

        if (!$pending) {
            return back()->with('error', 'The verification code has expired. Please register again.');
        }

        if (now()->greaterThan($pending['expires_at'])) {
            Cache::forget($key);
            return back()->with('error', 'The verification code has expired. Please request a new one.');
        }

        $enteredOtp = implode('', $request->otp);

        if (($pending['otp'] ?? null) !== $enteredOtp) {
            return back()->with('error', 'Invalid verification code.');
        }

        // Create user after correct OTP
        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'phone' => $pending['phone'] ?? null,
            'password' => $pending['password'],
            'email_verified_at' => now(),
            'otp' => null,
        ]);

        event(new Registered($user));

        Cache::forget($key);

        // Send success email



Mail::to($user->email)->send(
    new AccountVerifiedMail($user->name)
);


        return redirect()->route('login')
            ->with('success', 'Your email has been verified. You can now log in.');
    }





}
