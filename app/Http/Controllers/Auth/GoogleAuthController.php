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

    public function redirectToGoogle(){

        return Socialite::driver("google")->redirect();

    }


    public function handleGoogleCallback(){
        $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => Hash::make(Str::random(14)),
                'email_verified_at' => now(),
                'otp' => rand(100000, 999999)
            ]
        );

        Auth::login($user);
        return redirect()->intended('/')->with('success', 'You are in');

    }
}
