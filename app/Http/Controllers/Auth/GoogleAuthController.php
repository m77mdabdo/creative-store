<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Throwable;

/**
 * Sign in with Google.
 *
 * - The OAuth state is kept in the session, so a callback only completes a
 *   flow that this browser started.
 * - Accounts are matched by Google's stable account id. The first time a
 *   Google account is used, it is linked by email only when Google says
 *   the address is verified.
 * - Disabled and deleted accounts cannot sign in this way, the same as
 *   with a password.
 * - Signing in with Google never changes the account's password or name.
 */
class GoogleAuthController extends Controller
{
    private const LOCALE_KEY = 'google_login_locale';

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $request->session()->put(self::LOCALE_KEY, app()->getLocale());

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $locale = $this->locale($request);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::info('Google sign-in was not completed.', ['reason' => class_basename($e)]);

            return $this->failed($locale, 'site.google_failed');
        }

        $googleId = (string) $googleUser->getId();
        $email = Str::lower(trim((string) $googleUser->getEmail()));

        if ($googleId === '' || $email === '') {
            return $this->failed($locale, 'site.google_failed');
        }

        if (! $this->emailIsVerified($googleUser)) {
            return $this->failed($locale, 'site.google_unverified');
        }

        $user = User::withTrashed()->where('google_id', $googleId)->first()
            ?? User::withTrashed()->where('email', $email)->first();

        if ($user?->trashed()) {
            return $this->failed($locale, 'site.account_deleted');
        }

        if ($user && $user->google_id !== null && $user->google_id !== $googleId) {
            return $this->failed($locale, 'site.google_other_account');
        }

        if ($user && $user->is_active === false) {
            return $this->failed($locale, 'site.account_disabled');
        }

        $user = $user ? $this->link($user, $googleId) : $this->register($googleUser, $googleId, $email);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(LaravelLocalization::getLocalizedURL($locale, url('/')))
            ->with('success', __('You are in'));
    }

    private function emailIsVerified(SocialiteUser $googleUser): bool
    {
        $raw = method_exists($googleUser, 'getRaw') ? (array) $googleUser->getRaw() : [];
        $verified = $raw['email_verified'] ?? $raw['verified_email'] ?? false;

        return in_array($verified, [true, 1, '1', 'true'], true);
    }

    private function link(User $user, string $googleId): User
    {
        $user->forceFill([
            'google_id'         => $googleId,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        return $user;
    }

    private function register(SocialiteUser $googleUser, string $googleId, string $email): User
    {
        return DB::transaction(function () use ($googleUser, $googleId, $email) {
            $user = User::create([
                'name'      => Str::limit(trim((string) $googleUser->getName()) ?: Str::before($email, '@'), 120, ''),
                'email'     => $email,
                'google_id' => $googleId,
                'password'  => Hash::make(Str::random(40)),
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();

            $customer = Role::query()->where('name', 'customer')->first();
            if ($customer) {
                $user->roles()->syncWithoutDetaching([$customer->id]);
            }

            return $user;
        });
    }

    private function failed(string $locale, string $messageKey): RedirectResponse
    {
        return redirect()->to(LaravelLocalization::getLocalizedURL($locale, route('login')))
            ->with('error', __($messageKey));
    }

    private function locale(Request $request): string
    {
        $locale = (string) $request->session()->pull(self::LOCALE_KEY, app()->getLocale());

        return array_key_exists($locale, LaravelLocalization::getSupportedLocales()) ? $locale : (string) config('app.locale');
    }
}
