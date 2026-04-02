<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MyAccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('myaccount.myaccount', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->name = $request->name;
        $user->save();

        return back()->with('success_profile', __('account.name_updated_successfully'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
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
}
