<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Mail\AccountVerifiedMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VerifyAccountController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    public function __invoke(VerifyAccountRequest $request): RedirectResponse
    {
        $key = RegisterController::cacheKey($request->email);
        $pending = Cache::get($key);

        if (! $pending || now()->greaterThan($pending['expires_at'])) {
            Cache::forget($key);

            return back()->with('error', __('The verification code has expired. Please register again.'));
        }

        $enteredOtp = implode('', $request->otp);

        if (! hash_equals((string) ($pending['otp'] ?? ''), $enteredOtp)) {
            return $this->registerFailedAttempt($key, $pending);
        }

        Cache::forget($key);

        if (User::withTrashed()->where('email', $pending['email'])->exists()) {
            return redirect()->route('login')
                ->with('success', __('This email is already registered. Please log in.'));
        }

        $user = DB::transaction(function () use ($pending) {
            $user = User::create([
                'name'              => $pending['name'],
                'email'             => $pending['email'],
                'phone'             => $pending['phone'] ?? null,
                'password'          => $pending['password'],
                'email_verified_at' => now(),
            ]);

            Role::firstOrCreate(['name' => 'customer']);
            $user->assignRole('customer');

            return $user;
        });

        event(new Registered($user));

        Mail::to($user->email)->queue(new AccountVerifiedMail($user->name));

        return redirect()->route('login')
            ->with('success', __('Your email has been verified. You can now log in.'));
    }

    private function registerFailedAttempt(string $key, array $pending): RedirectResponse
    {
        $pending['attempts'] = ($pending['attempts'] ?? 0) + 1;

        if ($pending['attempts'] >= self::MAX_ATTEMPTS) {
            Cache::forget($key);

            return back()->with('error', __('Too many incorrect attempts. Please register again.'));
        }

        Cache::put($key, $pending, $pending['expires_at']);

        return back()->with('error', __('Invalid verification code.'));
    }
}
