<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $authenticated = Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember'),
        );

        if (! $authenticated) {
            return back()
                ->withErrors(['email' => __('site.invalid_email_or_password')])
                ->onlyInput('email');
        }

        $user = $request->user();

        if ($user->is_active === false) {
            $this->endSession($request);

            return back()
                ->withErrors(['email' => __('site.account_disabled')])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $user->isDashboardUser()
            ? redirect()->intended(route('dashboard.home'))
            : redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->endSession($request);

        return redirect()->route('home');
    }

    private function endSession(Request $request): void
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
