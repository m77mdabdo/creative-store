<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class GoogleAuthController extends Controller
{
    //

public function redirectToGoogle()
{
    return Socialite::driver('google')->stateless()->redirect();
}

public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    $email = $googleUser->getEmail();
    if (!$email) {
        return redirect('/login')->with('error', __('Google did not return an email address.'));
    }

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $googleUser->getName() ?? 'Google User',
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(32)),
            'otp' => null,
        ]
    );

    if (!$user->password) {
        $user->password = Hash::make(Str::random(14));
        $user->save();
    }

    Auth::login($user);

    return redirect()->intended('/')->with('success', __('You are in'));
}

}
