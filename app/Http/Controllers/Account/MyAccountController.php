<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Mail\ConfirmEmailChangeMail;
use App\Models\Order;
use App\Models\User;
use App\Services\Auth\AccountSessions;
use App\Support\Auth\EmailChangeLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * The signed-in person's own profile, for customers and team members alike:
 * photo, name, email, phone, password and a short summary of their orders.
 */
class MyAccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('roles:id,name');

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get(['id', 'order_number', 'status', 'total', 'currency', 'created_at', 'estimated_delivery_from', 'estimated_delivery_to', 'shipping_status']);

        $stats = [
            'orders'    => Order::query()->where('user_id', $user->id)->count(),
            'spent'     => (float) Order::query()->where('user_id', $user->id)->where('payment_status', 'paid')->sum('total'),
            'addresses' => $user->addresses()->count(),
            'wishlist'  => $user->wishlist()->withCount('items')->first()?->items_count ?? 0,
        ];

        return view('myaccount.myaccount', compact('user', 'orders', 'stats'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill([
            'name'  => $data['name'],
            'phone' => $request->normalizedPhone(),
        ]);

        // A new sign-in email takes effect only after it is confirmed from
        // that mailbox, so nobody can claim an address they do not own.
        $newEmail = Str::lower($data['email']);
        $emailPending = $newEmail !== Str::lower((string) $user->email);

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user->avatar_path);
            $user->avatar_path = $request->file('avatar')->store('avatars', config('filesystems.media_disk', 'public'));
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($user->avatar_path);
            $user->avatar_path = null;
        }

        $user->save();

        if ($emailPending) {
            Mail::to($newEmail)->send(new ConfirmEmailChangeMail(
                $user->name,
                EmailChangeLink::for($user, $newEmail),
                EmailChangeLink::VALID_MINUTES,
            ));

            return back()
                ->with('success_profile', __('account.profile_updated'))
                ->with('info_profile', __('account.email_change.sent', ['email' => $newEmail]));
        }

        return back()->with('success_profile', __('account.profile_updated'));
    }

    /**
     * Opened from the confirmation email sent to the new address.
     */
    public function confirmEmail(Request $request, User $user): RedirectResponse
    {
        $email = Str::lower((string) $request->query('email'));

        if (! EmailChangeLink::isCurrent($user, $request->query('v'))
            || ! filter_var($email, FILTER_VALIDATE_EMAIL)
            || User::withTrashed()->whereKeyNot($user->getKey())->where('email', $email)->exists()) {
            return redirect()->route('login')->with('error', __('account.email_change.invalid'));
        }

        $user->forceFill(['email' => $email, 'email_verified_at' => now()])->save();

        return redirect()->route(auth()->id() === $user->getKey() ? 'myaccount' : 'login')
            ->with(auth()->id() === $user->getKey() ? 'success_profile' : 'success', __('account.email_change.done'));
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => __('account.current_password_incorrect'),
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Other devices are signed out; this one stays signed in.
        app(AccountSessions::class)->revokeOthers($user, $request->session()->getId());

        return back()->with('success_password', __('account.password_updated_successfully'));
    }

    private function deleteAvatar(?string $path): void
    {
        if ($path && media_disk()->exists($path)) {
            media_disk()->delete($path);
        }
    }
}
