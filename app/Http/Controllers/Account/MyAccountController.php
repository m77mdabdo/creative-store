<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'email' => $data['email'],
            'phone' => $request->normalizedPhone(),
        ]);

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user->avatar_path);
            $user->avatar_path = $request->file('avatar')->store('avatars', config('filesystems.media_disk', 'public'));
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($user->avatar_path);
            $user->avatar_path = null;
        }

        $user->save();

        return back()->with('success_profile', __('account.profile_updated'));
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

        return back()->with('success_password', __('account.password_updated_successfully'));
    }

    private function deleteAvatar(?string $path): void
    {
        if ($path && media_disk()->exists($path)) {
            media_disk()->delete($path);
        }
    }
}
