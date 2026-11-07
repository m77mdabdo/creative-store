<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Auth\StaffWelcomeLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Where a new team member lands from their invitation: they choose their
 * own password and go straight into the dashboard.
 */
class StaffWelcomeController extends Controller
{
    public function show(Request $request, User $user): View|RedirectResponse
    {
        if (! $this->linkIsUsable($request, $user)) {
            return $this->expired();
        }

        return view('auth.reset-password', [
            'formAction' => $request->fullUrl(),
            'email'      => $user->email,
            'heading'    => __('staff.welcome.heading', ['name' => $user->name]),
            'intro'      => __('staff.welcome.intro'),
            'button'     => __('staff.welcome.button'),
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        if (! $this->linkIsUsable($request, $user)) {
            return $this->expired();
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user->forceFill([
            'password'          => $request->input('password'),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard.home')
            ->with('success', __('staff.welcome.done', ['name' => $user->name]));
    }

    private function linkIsUsable(Request $request, User $user): bool
    {
        return $user->is_active !== false
            && $user->isDashboardUser()
            && StaffWelcomeLink::isCurrent($user, $request->query('v'));
    }

    private function expired(): RedirectResponse
    {
        return redirect()->route('login')->with('info', __('staff.welcome.expired'));
    }
}
