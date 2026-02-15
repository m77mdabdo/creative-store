<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Models\User;

class VerifyAccountController extends Controller
{
    public function __invoke(VerifyAccountRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $enteredOtp = implode('', $request->otp);

        if ($user->otp !== $enteredOtp) {
            return back()->with('error', 'Invalid OTP or email address');
        }

        $user->email_verified_at = now();
        $user->otp = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Email verified successfully, you can login now');
    }
}
