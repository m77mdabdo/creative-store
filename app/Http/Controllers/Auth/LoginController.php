<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function login(LoginRequest $request): RedirectResponse
    {
        // لو LoginRequest فيه rules، دي هترجع validated data
        $credentials = $request->validated();

        // دعم remember me
        $remember = $request->boolean('remember');

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        )) {
            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ])
                ->onlyInput('email');
        }

        // مهم جداً للأمان بعد تسجيل الدخول
        $request->session()->regenerate();


        $user = $request->user();

        if ($user->hasRole('customer')) {
            return redirect()->route('home');
        }

        return redirect()->route('dashboard.home');
    }

}
