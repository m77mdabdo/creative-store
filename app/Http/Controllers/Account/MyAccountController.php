<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Requests\Account\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class MyAccountController extends Controller
{
    public function index(Request $request)
    {
        return view('myaccount.myaccount', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->name = $request->name;
        $user->save();

        return back()->with('success_profile', 'Name updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success_password', 'Password updated successfully.');
    }
}
